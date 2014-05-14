// Generated by CoffeeScript 1.7.1
(function() {
  var deleteHolidayDate, inverse;

  $("#addHolidayDate").click(function() {
    return $.ajax({
      method: 'GET',
      url: Routing.generate('OpitNotesLeaveBundle_admin_show_leave_date', {
        id: 0
      })
    }).done(function(data) {
      $('<div id="dialog-editholidaydate"></div>').html(data).dialog({
        title: '<i class="fa fa-list-alt"></i> Create Administrative Leave/Working Day',
        width: 750,
        modal: true,
        open: function() {
          return $(document).data('notes').funcs.initDateInputs($('#addholidaydate_frm'));
        },
        buttons: {
          Create: function() {
            return $.ajax({
              type: 'POST',
              global: false,
              url: Routing.generate('OpitNotesLeaveBundle_admin_add_leave_date', {
                id: 0
              }),
              data: $('#addholidaydate_frm').serialize()
            }).done(function(data) {
              var response;
              response = data;
              return $.ajax({
                type: 'POST',
                global: false,
                url: Routing.generate('OpitNotesLeaveBundle_admin_list_leave_dates'),
                data: {
                  "showList": 1
                }
              }).done(function(data) {
                var validationResult;
                $('#list-table').html(data);
                validationResult = $(document).data('notes').funcs.showAlert(response, "create", "Administrative Leave/Working Day created successfully");
                if (validationResult === true) {
                  return $('#dialog-editholidaydate').dialog("destroy");
                }
              });
            });
          },
          Close: function() {
            $('#dialog-editholidaydate').dialog("destroy");
          }
        }
      });
      return;
    });
  });

  $("#list-table").on("click", ".list-holidaydate", function(event) {
    var id;
    event.preventDefault();
    id = $(this).attr("data-id");
    return $.ajax({
      method: 'GET',
      url: Routing.generate('OpitNotesLeaveBundle_admin_show_leave_date', {
        id: id
      })
    }).done(function(data) {
      $('<div id="dialog-editholidaydate"></div>').html(data).dialog({
        title: '<i class="fa fa-list-alt"></i> Edit Administrative Leave/Working Day',
        width: 750,
        modal: true,
        open: function() {
          return $(document).data('notes').funcs.initDateInputs($('#addholidaydate_frm'));
        },
        buttons: {
          Save: function() {
            return $.ajax({
              type: 'POST',
              global: false,
              url: Routing.generate('OpitNotesLeaveBundle_admin_add_leave_date', {
                id: id
              }),
              data: $('#addholidaydate_frm').serialize()
            }).done(function(data) {
              var response;
              response = data;
              return $.ajax({
                type: 'POST',
                global: false,
                url: Routing.generate('OpitNotesLeaveBundle_admin_list_leave_dates'),
                data: {
                  "showList": 1
                }
              }).done(function(data) {
                var validationResult;
                $('#list-table').html(data);
                validationResult = $(document).data('notes').funcs.showAlert(response, "create", "Administrative Leave/Working Day modified successfully");
                if (validationResult === true) {
                  return $('#dialog-editholidaydate').dialog("destroy");
                }
              });
            });
          },
          Close: function() {
            $('#dialog-editholidaydate').dialog("destroy");
          }
        }
      });
      return;
    });
  });

  $('.year').click(function(event) {
    $('.year').removeClass('selected-page');
    $(this).addClass('selected-page');
    return $.ajax({
      method: 'POST',
      url: Routing.generate('OpitNotesLeaveBundle_admin_list_leave_dates'),
      data: {
        'showList': 1,
        'year': $(this).data('year')
      }
    }).done(function(data) {
      $('#list-table').html(data);
    });
  });

  $('#delete').click(function() {
    return deleteHolidayDate();
  });

  $('#list-table').on("click", ".delete-single-holidaydate", function(event) {
    var $checkbox;
    event.preventDefault();
    $checkbox = $(this).closest('tr').find(':checkbox');
    $checkbox.prop('checked', true);
    return deleteHolidayDate();
  });

  deleteHolidayDate = function() {
    var url;
    url = Routing.generate('OpitNotesLeaveBundle_admin_delete_leave_date');
    return $(document).data('notes').funcs.deleteAction('Holiday date delete', 'holiday date(s)', url, '.list-delete-holidaydate');
  };

  $('#list-table').on("click", "th .fa-trash-o", function() {
    var group;
    group = $(this).closest('table').attr('id');
    return $("#" + group + " .list-delete-holidaydate").filter(function() {
      return !this.disabled;
    }).checkAll();
  });

  inverse = false;

  $('form').on('click', '.fa-sort', function() {
    return inverse = $(document).data('notes').funcs.clientSideListOrdering($(this), inverse);
  });

  $('form').on('click', '.order-text', function() {
    return inverse = $(document).data('notes').funcs.clientSideListOrdering($(this).parent().find('i'), inverse);
  });

  $(document).ready(function() {
    $('.year').bind('mousedown', false);
    return $('.year:first').addClass('selected-page');
  });

}).call(this);
