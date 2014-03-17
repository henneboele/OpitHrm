// Generated by CoffeeScript 1.7.1
(function() {
  var deleteGroup, inverse, showRoleDialog;

  $('#reply-message').css({
    display: 'none'
  });

  showRoleDialog = function(id, name, description, url, title, flashMessage) {
    var selfDialog;
    $('#dialog-edititem h2').html(title);
    $('#dialog-edititem h2').addClass('dialog-h2');
    $('.dialog-description').html(description);
    selfDialog = $('<div>');
    selfDialog.html($("#" + valueForm).html());
    selfDialog.find("#" + valueField).val(name);
    selfDialog.dialog({
      width: 400,
      modal: true,
      title: title,
      buttons: {
        Create: function() {
          var value;
          if (selfDialog.find("#" + valueField).val()) {
            value = selfDialog.find("#" + valueField).val();
            return $.ajax({
              type: 'POST',
              url: Routing.generate(url, {
                id: id
              }),
              data: {
                'value': value
              }
            }).done(function(data) {
              if (data.duplicate) {
                $(document).data('notes').funcs.showAlert(data, 'create', "" + propertyName + " already exists", true);
              } else {
                $('#list-table').replaceWith(data);
                $(document).data('notes').funcs.showAlert(data, 'create', flashMessage);
              }
              return selfDialog.dialog('destroy');
            });
          } else {
            return selfDialog.find('#reply-message').css({
              display: 'block'
            });
          }
        },
        Close: function() {
          return selfDialog.dialog('destroy');
        }
      }
    });
    if (name) {
      return $('.ui-dialog-buttonset .ui-button:first-child .ui-button-text').text('Edit');
    }
  };

  deleteGroup = function(id, name) {
    var selfDialog;
    if (!!name) {
      selfDialog = $('<div>');
      selfDialog.html("Are you sure you want to delete " + propertyName + "(s) \"" + name + "\"?");
      return selfDialog.dialog({
        width: 400,
        modal: true,
        title: "Delete " + propertyName,
        buttons: {
          Continue: function() {
            $.ajax({
              type: 'POST',
              url: Routing.generate(removeUrl),
              data: {
                id: id
              }
            }).done(function(data) {
              if (data.userRelated) {
                return $(document).data('notes').funcs.showAlert(data, 'create', "Deletion not allowed for " + propertyName + "(s) with relations", true);
              } else {
                $('#list-table').replaceWith(data);
                return $(document).data('notes').funcs.showAlert(data, 'create', "" + propertyNameCapital + "(s) successfully deleted!");
              }
            });
            return selfDialog.dialog('destroy');
          },
          Cancel: function() {
            $('#list-table').find('input:checkbox').each(function() {
              return $(this).attr('checked', false);
            });
            return selfDialog.dialog('destroy');
          }
        }
      });
    }
  };

  $('#main-wrapper').on('click', '#add', function() {
    return showRoleDialog('new', '', "Create a new " + propertyName + ".", url, "Create " + propertyName, "" + propertyNameCapital + " successfully created!");
  });

  $('#main-wrapper').on('click', '.edit-group', function() {
    var id, name;
    id = $(this).closest('tr').children('td:nth-child(2)').html();
    name = $(this).closest('tr').children('td:nth-child(3)').html();
    return showRoleDialog(id, name, "Edit selected " + propertyName + ".", url, "Edit " + propertyName, "" + propertyNameCapital + " successfully edited!");
  });

  $('#main-wrapper').on('click', '.remove-group', function() {
    var id, name, parentTr;
    parentTr = $(this).closest('tr');
    name = parentTr.children('td:nth-child(3)').html();
    id = parentTr.children('td:nth-child(2)').html();
    parentTr.find('input').attr('checked', true);
    return deleteGroup(id, name);
  });

  $('#delete').on('click', function() {
    var ids, names;
    ids = [];
    names = '';
    $('.list-delete-user').each(function() {
      var parentTr;
      if ($(this).prop('checked')) {
        parentTr = $(this).closest('tr');
        ids.push(parentTr.children('td:nth-child(2)').html());
        return names += parentTr.children('td:nth-child(3)').html() + ', ';
      }
    });
    names = names.substring(0, names.length - 2);
    return deleteGroup(ids, names);
  });

  $('th .fa-trash-o').on('click', function() {
    return $('.list-delete-user:enabled').checkAll();
  });

  inverse = false;

  $('form').on('click', '.fa-sort', function() {
    return inverse = $(document).data('notes').funcs.clientSideListOrdering($(this), inverse);
  });

  $('form').on('click', '.order-text', function() {
    return inverse = $(document).data('notes').funcs.clientSideListOrdering($(this).parent().find('i'), inverse);
  });

}).call(this);
