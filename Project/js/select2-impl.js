$(document).ready(function() {
  
  // TODO: DRY - implementations are exactly the same. Would be cool to filter here too
  $('#add-group-users').select2({
    placeholder: "Add people",
    
    ajax: {
      url: "../web/get_users.php",
      dataType: 'json',
      delay: 250,
      data: function (term) {
        return {
            term: term
        };
      },
      processResults: function (response, params) {
        params.page = params.page || 1;
        
        return {
          results: $.map(response.data, function (item) {
            // filter by 'contains ignore case' search term
            return item.username.toUpperCase().indexOf(params.term.toUpperCase()) == -1 ?
              null : 
              {
                text: item.username,
                id: item.username
              };
          })
        };
      },
      cache: true
    },
    escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
    minimumInputLength: 3
  });
  
  $('#add-member-existing-group').select2({
    placeholder: "Add people",
    
    ajax: {
      url: "../web/get_users.php",
      dataType: 'json',
      delay: 250,
      data: function (term) {
        return {
            term: term
        };
      },
      processResults: function (response, params) {
        params.page = params.page || 1;
        
        return {
          results: $.map(response.data, function (item) {
            // filter by 'contains ignore case' search term
            return item.username.toUpperCase().indexOf(params.term.toUpperCase()) == -1 ?
              null : 
              {
                text: item.username,
                id: item.username
              };
          })
        };
      },
      cache: true
    },
    escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
    minimumInputLength: 3
  });
  
});