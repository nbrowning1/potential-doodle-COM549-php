$(document).ready(function() {

  var currentUser;
  var users = [];
  var groups = [];
  
  $.ajax({
    type: "POST",
    url: '../chat/get_current_user.php',
    data: { },
    success: function(response) {
      if (response.user) {
        currentUser = response.user;
      }
      
      // HARD refresh at the start - results of the promise because of the ajax call to get users & groups in the first place
      UsersGroupsRefresh.refreshUsers()
        .then(function(updatedUsers) {
          populateUsers(updatedUsers);
        });
      UsersGroupsRefresh.refreshGroups()
        .then(function(updatedGroups) {
          populateGroups(updatedGroups);
        });
    }
  });
  
  // occasional polling for updates
  // is this needed? users-groups-refresh does its own refresh so this can be a lot lower without straining any resources
  setInterval(refresh, 30000);
  
  // grab refreshed references from UsersGroupsRefresh (does its own refreshing for users & groups)
  function refresh() {
    users = [];
    groups = [];
    populateUsers(UsersGroupsRefresh.getUsers());
    populateGroups(UsersGroupsRefresh.getGroups());
  }
  
  function populateUsers(updatedUsers) {
    $.each(updatedUsers, function(i, user) {
      users.push(user.username);
    });
  };

  function populateGroups(updatedGroups) {
    $.each(updatedGroups, function(i, group) {
      groups.push(group);
    });
  };
  
  var userMatcher = function() {
    return function findMatches(q, cb) {
      var matches, substringRegex;

      matches = [];

      // regex used to determine if a string contains the substring `q`
      substrRegex = new RegExp(q, 'i');

      $.each(users, function(i, username) {
        if (substrRegex.test(username)) {
          matches.push(username);
        }
      });

      cb(matches);
    };
  };
  
  var groupMatcher = function() {
    return function findMatches(q, cb) {
      var matches, substringRegex;
      
      // return whole groups for more complex matching than just simple strings (unlike userMatcher)
      matches = [];

      // regex used to determine if a string contains the substring `q`
      substrRegex = new RegExp(q, 'i');

      $.each(groups, function(i, group) {
        if (substrRegex.test(group.groupname)) {
          matches.push(group);
        } else {
          // try matching against group members
          for (i = 0; i < group.members.length; i++) {
            if (substrRegex.test(group.members[i])) {
              matches.push(group);
              break;
            }
          }
        }
      });

      cb(matches);
    };
  };
  
  $('#search-new-conversation input')
    .typeahead({
      hint: true,
      highlight: true,
      minLength: 1
    },
    {
      name: 'users',
      source: userMatcher(),
      templates: {
        empty: [
          '<div class="tt-none" style="padding-left:20px;"><i>',
            'No results found',
          '</i></div>'
        ].join('\n')
      }
    },
    {
      name: 'groups',
      source: groupMatcher(),
      displayKey: function(group) {
        return group.groupname;
      },
      templates: {
        empty: [
          '<div class="tt-none" style="padding-left:20px;"><i>',
            'No results found',
          '</i></div>'
        ].join('\n'),
        suggestion: function (group) {
          var membersList = group.members.join(", ");  
          return '<p><b><u>Group:</u></b> ' + group.groupname + ': <i>' + membersList + '</i></p>';
        }
      }
    })
    .on('typeahead:selected', function(e, datum) {
      var form = $('#search-conversation-submit');
      var groupSearchInput = $('#group-search');
    
      var groupName = datum.groupname;
      if (groupName) {
        // indicate which group we're searching for
        groupSearchInput.val(datum.groupname);
      } else {
        groupSearchInput.val('');
      }
    
      form.submit();
    })
    .on('typeahead:render', function(e, objs, async, name) {
      // needed to only show one 'No results' message for the multiple datasets (users & groups) since the empty template has to be defined per dataset
      var noResultsEls = $('.tt-none');
      var numSuggestions = $('.tt-suggestion.tt-selectable').length;

      // Hide all notFound messages
      noResultsEls.hide();
      // Only show the first message if there are zero results available
      if (numSuggestions === 0) {
          noResultsEls.first().show();
      }
    });
  
});