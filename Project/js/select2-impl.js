$(document).ready(function() {
  
  $('#add-group-users').select2({
    placeholder: "Add people",
    
    ajax: {
      url: "../cache/users.json",
      dataType: 'json',
      delay: 250,
      data: function (term) {
        return {
            term: term
        };
      },
      processResults: function (data, params) {
        params.page = params.page || 1;
        
        return {
          results: $.map(data, function (item) {
            // filter by 'contains' search term
            return item.username.indexOf(params.term) == -1 ?
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