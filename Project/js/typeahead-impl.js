$(document).ready(function() {

  // constructs the suggestion engine
  var users = new Bloodhound({
    datumTokenizer: function(user) { 
      return Bloodhound.tokenizers.whitespace(user.username);
    },
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    prefetch: '../cache/users.json'
  });
  
  var groups = new Bloodhound({
    datumTokenizer: function(group) { 
      return Bloodhound.tokenizers.whitespace(group.groupname);
    },
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    prefetch: '../cache/groups.json'
  });

  users.clearPrefetchCache();
  groups.clearPrefetchCache();
  
  $('#search-new-conversation input')
    .typeahead({
      hint: true,
      highlight: true,
      minLength: 1
    },
    {
      name: 'users',
      displayKey: function(user) {
        return user.username;        
      },
      source: users

    },
    {
      name: 'groups',
      displayKey: function(group) {
        return group.groupname;
      },
      source: groups,
      templates: {
        suggestion: function (group) {
          var membersList = group.members.join(", ");  
          return '<p><b>Group:</b> ' + group.groupname + ': <i>' + membersList + '</i></p>';
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