// Generated by CoffeeScript 1.6.3
(function() {
  var closeAlert, showAlert;

  $("#add").click(function() {
    return $.ajax({
      method: 'GET',
      url: Routing.generate('OpitNotesUserBundle_user_show', {
        id: 0
      })
    }).done(function(data) {
      $('<div id="dialog-edititem"></div>').dialog({
        open: function() {
          $('.ui-dialog-title').append('<i class="fa fa-list-alt"></i> Create User');
          return $(this).html(data);
        },
        dialogClass: 'popup-dialog',
        width: 750,
        modal: true,
        buttons: {
          Create: function() {
            return $.ajax({
              type: 'POST',
              global: false,
              url: Routing.generate('OpitNotesUserBundle_user_add', {
                id: 0
              }),
              data: $('#adduser_frm').serialize()
            }).done(function(data) {
              var response;
              response = data;
              return $.ajax({
                type: 'POST',
                global: false,
                url: Routing.generate('OpitNotesUserBundle_user_list'),
                data: {
                  "showList": 1
                }
              }).done(function(data) {
                $('#list-table').html(data);
                return showAlert(response, "create", "User created successful");
              });
            });
          },
          Close: function() {
            $('#dialog-edititem').dialog("destroy");
          }
        }
      });
      return;
    });
  });

  $("#list-table").on("click", ".list-username", function() {
    var id;
    event.preventDefault();
    id = $(this).attr("data-user-id");
    return $.ajax({
      method: 'GET',
      url: Routing.generate('OpitNotesUserBundle_user_show', {
        id: id
      })
    }).done(function(data) {
      $('<div id="dialog-edititem"></div>').dialog({
        open: function() {
          $('.ui-dialog-title').append('<i class="fa fa-list-alt"></i> Edit User');
          return $(this).html(data);
        },
        dialogClass: 'popup-dialog',
        width: 750,
        modal: true,
        buttons: {
          Save: function() {
            return $.ajax({
              type: 'POST',
              global: false,
              url: Routing.generate('OpitNotesUserBundle_user_add', {
                id: id
              }),
              data: $('#adduser_frm').serialize()
            }).done(function(data) {
              var response;
              response = data;
              return $.ajax({
                type: 'POST',
                global: false,
                url: Routing.generate('OpitNotesUserBundle_user_list'),
                data: {
                  "showList": 1
                }
              }).done(function(data) {
                $('#list-table').html(data);
                return showAlert(response, "update", "User modified successful");
              });
            });
          },
          Close: function() {
            $('#dialog-edititem').dialog("destroy");
          }
        }
      });
      return;
    });
  });

  $("#list-table").on("click", ".list-change-password", function() {
    var id;
    event.preventDefault();
    id = $(this).attr("data-user-id");
    return $.ajax({
      method: 'GET',
      url: Routing.generate('OpitNotesUserBundle_user_show_password', {
        id: id
      })
    }).done(function(data) {
      $('<div id="dialog-edititem"></div>').dialog({
        open: function() {
          $('.ui-dialog-title').append('<i class="fa fa-list-alt"></i> Reset Password');
          return $(this).html(data);
        },
        dialogClass: 'popup-dialog-change-password',
        width: 750,
        modal: true,
        buttons: {
          Save: function() {
            return $.ajax({
              type: 'POST',
              global: false,
              url: Routing.generate('OpitNotesUserBundle_user_update_password', {
                id: id
              }),
              data: $('#changePassword_frm').serialize()
            }).done(function(data) {
              var response;
              response = data;
              return showAlert(response, "update", "Password reset successful");
            });
          },
          Close: function() {
            $('#dialog-edititem').dialog("destroy");
          }
        }
      });
      return;
    });
  });

  $('#delete').click(function() {
    var users;
    users = [];
    $('.list-delete-user').each(function() {
      if (this.checked) {
        return users.push($(this).val());
      }
    });
    return $.ajax({
      type: 'POST',
      global: false,
      url: Routing.generate('OpitNotesUserBundle_user_delete'),
      data: {
        "userIds": users
      }
    }).done(function(data) {
      var response;
      response = data;
      return $.ajax({
        type: 'POST',
        global: false,
        url: Routing.generate('OpitNotesUserBundle_user_list'),
        data: {
          "showList": 1
        }
      }).done(function(data) {
        $('#list-table').html(data);
        showAlert(response, "delete", "deleted the user(s)");
      });
    });
  });

  showAlert = function(response, actionType, message) {
    var errorString, i, _i, _len, _ref;
    $('#reply-message').addClass("alert-message");
    if (response[0].response === 'error') {
      if ("update" === actionType || "create" === actionType) {
        errorString = "<ul>";
        _ref = response[0].errorMessage;
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
          i = _ref[_i];
          errorString += "<li>" + i + "</li>";
        }
        errorString += "</ul>";
        return $('#reply-message').html("<i class='fa fa-exclamation-triangle'></i> <strong>Error messages:</strong>" + errorString).removeClass('success-message').addClass('error-message');
      } else if ("delete" === actionType) {
        return $('#list-reply-message').html("<i class='fa fa-exclamation-triangle'></i> Error, while tried to delete the user(s)! <i class='float-right fa fa-chevron-circle-up'></i> ").removeClass('success-message').addClass('error-message').fadeIn(200).delay(5000).slideUp(1000);
      }
    } else {
      $('#list-reply-message').html("<i class='fa fa-check-square'></i> " + message + "! <i class='float-right fa fa-chevron-circle-up'></i> ").addClass("alert-message").addClass('success-message').fadeIn(200).delay(2000).slideUp(1000);
      return $('#dialog-edititem').dialog('destroy');
    }
  };

  closeAlert = function() {
    console.log("click");
    return $('#list-reply-message').hide;
  };

  $(document).ready(function() {
    return $('#list-reply-message').click(function() {
      return closeAlert();
    });
  });

}).call(this);
