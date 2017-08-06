function serializeForm(formSelector) {
  return $(formSelector).serializeArray().reduce(function(obj, item) {
      if (obj[item.name]) {
          if (obj[item.name].constructor === Array) {
              obj[item.name].push(item.value);
          } else {
              var temp = obj[item.name];
              obj[item.name] = [];
              obj[item.name].push(temp);
              obj[item.name].push(item.value);
          }
      } else {
          obj[item.name] = item.value;
      }
      return obj;
  }, {});
}

function getActiveConversationEl() {
  return document.getElementsByClassName('active')[0];
}