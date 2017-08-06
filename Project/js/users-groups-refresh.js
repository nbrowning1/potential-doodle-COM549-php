$(document).ready(function() {
  UsersGroupsRefresh.refreshUsers();
  UsersGroupsRefresh.refreshGroups();
  
  setInterval(function() {
    UsersGroupsRefresh.refreshUsers();
    UsersGroupsRefresh.refreshGroups();
  }, 30000);
});

var UsersGroupsRefresh = (function() {

  var users = [];
  var groups = [];
  
  function _getUsers() {
    return users;
  }

  function _getGroups() {  
    return groups;
  }
  
  function _refreshUsers() {
    return new Promise(function(resolve, reject) {
      $.ajax({
        url: '../web/get_users.php',
        type: 'POST',
        data: { },
        success: function(response) {
          if (response.success) {
            users = response.data;
          }
          resolve(_getUsers());
        },
        error: function (request, status, error) {
          console.log('Error refreshing users: ' + request.responseText);
          reject(null);
        }
      });
    });
  }
  
  function _refreshGroups() {
    return new Promise(function(resolve, reject) {
      $.ajax({
        url: '../web/get_groups.php',
        type: 'POST',
        data: { },
        success: function(response) {
          if (response.success) {
            groups = response.data;
          }
          resolve(_getGroups());
        },
        error: function (request, status, error) {
          console.log('Error refreshing groups: ' + request.responseText);
          reject(null);
        }
      });
    });
  }
  
  return {
    getUsers: function() {
      return _getUsers();
    },
    getGroups: function() {
      return _getGroups();
    },
    refreshUsers: function() {
      return _refreshUsers();
    },
    refreshGroups: function() {
      return _refreshGroups();
    }
  };
  
})();