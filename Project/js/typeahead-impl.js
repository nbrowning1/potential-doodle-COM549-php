$(document).ready(function() {

  var currentUser;
  var users = [];
  var groups = [];
  
  // TODO: more often refresh - only refreshes on page load
  // also probably worth grabbing from DB since the cache does sweet fa to help requests now (bc prefetch is fucking garbage)
  $.ajax({
    type: "POST",
    url: '../web/get_current_user.php',
    data: { },
    success: function(response) {
      if (response.user) {
        currentUser = response.user;
      }
      populateUsers();
      populateGroups();
    }
  });
  
  var populateUsers = function() {
    $.ajax({
      type: "GET",
      url: '../cache/users.json',
      data: { },
      success: function(response) {
        $.each(response, function(i, user) {
          // exclude self
          if (currentUser !== user.username) {
            users.push(user.username);
          }
        });
      }
    });
  };

  var populateGroups = function() {
    $.ajax({
      type: "GET",
      url: '../cache/groups.json',
      data: { },
      success: function(response) {
        $.each(response, function(i, group) {
          // exclude groups not containing user
          if ($.inArray(currentUser, group.members) > -1) {
            groups.push(group);
          }
        });
      }
    });
  };
  
  var userMatcher = function(usernames) {
    return function findMatches(q, cb) {
      var matches, substringRegex;

      matches = [];

      // regex used to determine if a string contains the substring `q`
      substrRegex = new RegExp(q, 'i');

      $.each(usernames, function(i, username) {
        if (substrRegex.test(username)) {
          matches.push(username);
        }
      });

      cb(matches);
    };
  };
  
  var groupMatcher = function(groups) {
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
      source: userMatcher(users)
    },
    {
      name: 'groups',
      source: groupMatcher(groups),
      displayKey: function(group) {
        return group.groupname;
      },
      templates: {
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
    });
  
});