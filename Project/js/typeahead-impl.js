$(document).ready(function() {

  // constructs the suggestion engine
  var users = new Bloodhound({
    datumTokenizer: function(user) { 
      return Bloodhound.tokenizers.whitespace(user.username);
    },
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    prefetch: '../cache/users.json'
  });

  /*users.clearPrefetchCache();
  users.initialize(true);*/
  
  console.log(users);
  
  $('#search-new-conversation input')
    .typeahead({
      hint: true,
      highlight: true,
      minLength: 1
    },
    {
      name: 'users',
      displayKey: function(users) {
        return users.username;        
      },
      source: users

    })
    .on('typeahead:selected', function(e){
      e.target.form.submit();
    });
  
});