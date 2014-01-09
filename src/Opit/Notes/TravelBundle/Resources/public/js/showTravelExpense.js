// Generated by CoffeeScript 1.6.3
(function() {
  var $addCompanyTagLink, $addUserTagLink, $expensesPaidByMe, $expensesPaidByOpit, $form, $formFieldset, $generalFormFieldset, $perDiem, addNewForm, createDeleteButton, createTableRow, getHoursBetween, reCreateExpenses;

  createDeleteButton = function() {
    var $deleteButton;
    $deleteButton = $('<div>');
    $deleteButton.addClass('deleteFormFieldsetChild formFieldsetButton').html('<i class="fa fa-minus-square"></i>Delete');
    $deleteButton.on('click', function() {
      return $(this).parent().remove();
    });
    return $deleteButton;
  };

  reCreateExpenses = function(self) {
    var $container, $selectedExpense;
    $selectedExpense = $('<span>').addClass('selected-expense');
    $selectedExpense.html(self.find('select').find(':selected').text());
    $container = $('<div>').addClass('formFieldsetChild');
    self.children('label:first').remove();
    $container.append(self);
    $container.append(createDeleteButton());
    $container.prepend($selectedExpense);
    return $container;
  };

  addNewForm = function(collectionHolder, parent) {
    var $formFieldsetChild, $selectedExpense, index, newForm, prototype;
    event.preventDefault();
    prototype = collectionHolder.data('prototype');
    index = collectionHolder.data('index');
    prototype = prototype.replace('<label class="required">__name__label__</label>', '');
    newForm = prototype.replace(/__name__/g, index);
    $selectedExpense = $('<span>').addClass('selected-expense');
    $selectedExpense.html('Expense type');
    $formFieldsetChild = $('<div>').addClass('formFieldsetChild');
    $formFieldsetChild.append(newForm);
    $formFieldsetChild.append(createDeleteButton());
    $formFieldsetChild.prepend($selectedExpense);
    collectionHolder.data('index', index + 1);
    return parent.append($formFieldsetChild);
  };

  createTableRow = function(text, value, rowTitle) {
    var $row, $textColumn, $valueColumn;
    $row = $('<tr>');
    $textColumn = $('<td>');
    $textColumn.addClass('bgGrey bold');
    $textColumn.html(text + ' <i class="fa fa-clock-o" title="' + rowTitle + '"></i>');
    $valueColumn = $('<td>');
    $valueColumn.text(value + ' EUR');
    if (text === 'Total') {
      $textColumn.html('');
      $valueColumn.html('<strong>Total</strong><br /> ' + value + ' EUR');
    }
    $row.append($textColumn);
    $row.append($valueColumn);
    return $row;
  };

  $perDiem = $('<div>');

  getHoursBetween = function(departureDate, departureHour, departureMinute, arrivalDate, arrivalHour, arrivalMinute) {
    var arrival, departure;
    departure = new Date("" + departureDate + " " + departureHour + ":" + departureMinute);
    arrival = new Date("" + arrivalDate + " " + arrivalHour + ":" + arrivalMinute);
    return $.ajax({
      method: 'POST',
      url: Routing.generate('OpitNotesTravelBundle_expense_perdiem'),
      data: {
        arrival: arrival,
        departure: departure
      }
    }).done(function(data) {
      var $perDiemAmount, $perDiemDay, $perDiemHeader, $perDiemTable;
      $('.perDiemTable').remove();
      $perDiemTable = $('<table>');
      $perDiemTable.addClass('perDiemTable bordered');
      $perDiemHeader = $('<tr>');
      $perDiemDay = $('<th>');
      $perDiemDay.text('Day');
      $perDiemAmount = $('<th>');
      $perDiemAmount.text('Amount');
      $perDiemHeader.append($perDiemDay);
      $perDiemHeader.append($perDiemAmount);
      $perDiemTable.append($perDiemHeader);
      if (data['totalTravelHoursOnSameDay'] > 0) {
        $perDiemTable.append(createTableRow('Travel hours', data['totalTravelHoursOnSameDay'], ""));
        return $perDiemTable.append(createTableRow('Total', data['totalPerDiem'], ""));
      } else {
        $perDiemTable.append(createTableRow('Departure', data['departurePerDiem'], "Number of hours traveled on departure day " + data['departureHours'] + "."));
        $perDiemTable.append(createTableRow("Full (" + data['daysBetween'] + ")", data['daysBetweenPerDiem'], "Number of full days " + data['daysBetween'] + "."));
        $perDiemTable.append(createTableRow('Arrival', data['arrivalPerDiem'], "Number of hours traveled on arrival day " + data['arrivalHours'] + "."));
        $perDiemTable.append(createTableRow('Total', data['totalPerDiem']));
        return $perDiem.append($perDiemTable);
      }
    });
  };

  $(document).ready(function() {
    var $arrivalHour, $arrivalMinute, $departureHour, $departureMinute, $perDiemAmountsTable, $perDiemTitle, $td, $tr, arrivalDate, arrivalDateVal, arrivalHourVal, arrivalMinuteVal, arrivalTime, companyPaidExpensesIndex, departureDate, departureDateVal, departureHourVal, departureMinuteVal, departureTime, userPaidExpensesIndex;
    arrivalDate = $('#travelExpense_arrivalDateTime_date');
    arrivalTime = $('#travelExpense_arrivalDateTime_time');
    departureDate = $('#travelExpense_departureDateTime_date');
    departureTime = $('#travelExpense_departureDateTime_time');
    arrivalDate.attr('readonly', 'readonly');
    departureDate.attr('readonly', 'readonly');
    arrivalTime.addClass('inlineElements time-picker');
    departureTime.addClass('inlineElements time-picker');
    arrivalDate.css({
      display: 'inline-block'
    });
    departureDate.css({
      display: 'inline-block'
    });
    $('#travelExpense').children('.formFieldset:nth-child(3)').append($addCompanyTagLink);
    $('#travelExpense').children('.formFieldset:nth-child(2)').append($addUserTagLink);
    companyPaidExpensesIndex = 0;
    userPaidExpensesIndex = 0;
    if ($('#travelExpense_companyPaidExpenses').children('div').length > 0) {
      $('#travelExpense_companyPaidExpenses').children('div').each(function() {
        var $container;
        $container = reCreateExpenses($(this));
        $('#travelExpense').children('.formFieldset:nth-child(3)').append($container);
        return companyPaidExpensesIndex++;
      });
    }
    if ($('#travelExpense_userPaidExpenses').children('div').length > 0) {
      $('#travelExpense_userPaidExpenses').children('div').each(function() {
        var $container;
        $container = reCreateExpenses($(this));
        $('#travelExpense').children('.formFieldset:nth-child(2)').append($container);
        return userPaidExpensesIndex++;
      });
    }
    $('#travelExpense_companyPaidExpenses').data('index', companyPaidExpensesIndex);
    $('#travelExpense_userPaidExpenses').data('index', userPaidExpensesIndex);
    $('#travelExpense_companyPaidExpenses').parent().children('label').remove();
    $('#travelExpense_userPaidExpenses').parent().children('label').remove();
    $('#travelExpense').css({
      display: 'block'
    });
    $perDiemAmountsTable = $('<table>');
    $perDiemAmountsTable.addClass('per-diem-amounts display-none');
    $.ajax({
      method: 'POST',
      url: Routing.generate('OpitNotesTravelBundle_expense_perdiemvalues')
    }).done(function(data) {
      var $tdAmount, $tdHours, $tr, key, value, _results;
      _results = [];
      for (key in data) {
        value = data[key];
        $tr = $('<tr>');
        $tdHours = $('<td>');
        $tdHours.attr('width', '100px');
        $tdHours.text("Over " + key + " hours");
        $tdAmount = $('<td>');
        $tdAmount.text(value + ' EUR');
        $tr.append($tdHours);
        $tr.append($tdAmount);
        _results.push($perDiemAmountsTable.append($tr));
      }
      return _results;
    });
    $tr = $('<tr>');
    $td = $('<td>');
    $td.attr('colspan', 2);
    $td.html('Per diem is given to employee considering the following slab.');
    $tr.append($td);
    $perDiemAmountsTable.prepend($tr);
    $perDiem.append($perDiemAmountsTable);
    $perDiemTitle = $('<h3>');
    $perDiemTitle.html('Per diem <i class="fa fa-question-circle"></i>');
    $perDiem.append($perDiemTitle);
    $perDiem.addClass('formFieldset');
    $perDiem.insertBefore($('#travelExpense_add_travel_expense').parent());
    $('.fa-question-circle').on('mouseover', function() {
      return $('.per-diem-amounts').removeClass('display-none');
    });
    $('.fa-question-circle').on('mouseout', function() {
      return $('.per-diem-amounts').addClass('display-none');
    });
    $departureHour = $('#travelExpense_departureDateTime_time_hour');
    $departureMinute = $('#travelExpense_departureDateTime_time_minute');
    $arrivalHour = $('#travelExpense_arrivalDateTime_time_hour');
    $arrivalMinute = $('#travelExpense_arrivalDateTime_time_minute');
    departureDateVal = departureDate.val();
    departureHourVal = $departureHour.val();
    departureMinuteVal = $departureMinute.val();
    arrivalDateVal = arrivalDate.val();
    arrivalHourVal = $arrivalHour.val();
    arrivalMinuteVal = $arrivalMinute.val();
    $departureHour.on('change', function() {
      departureHourVal = $departureHour.val();
      return getHoursBetween(departureDateVal, departureHourVal, departureMinuteVal, arrivalDateVal, arrivalHourVal, arrivalMinuteVal);
    });
    $departureMinute.on('change', function() {
      departureMinuteVal = $departureMinute.val();
      return getHoursBetween(departureDateVal, departureHourVal, departureMinuteVal, arrivalDateVal, arrivalHourVal, arrivalMinuteVal);
    });
    $arrivalHour.on('change', function() {
      arrivalHourVal = $arrivalHour.val();
      return getHoursBetween(departureDateVal, departureHourVal, departureMinuteVal, arrivalDateVal, arrivalHourVal, arrivalMinuteVal);
    });
    return $arrivalMinute.on('change', function() {
      arrivalMinuteVal = $arrivalMinute.val();
      return getHoursBetween(departureDateVal, departureHourVal, departureMinuteVal, arrivalDateVal, arrivalHourVal, arrivalMinuteVal);
    });
  });

  $formFieldset = $('<div>');

  $formFieldset.addClass('formFieldset');

  $generalFormFieldset = $formFieldset.clone().addClass('generalFormFieldset');

  $expensesPaidByMe = $formFieldset.clone().append($('<h3>').html('Expenses paid by me'));

  $expensesPaidByOpit = $formFieldset.clone().append($('<h3>').html('Expenses paid by opit'));

  $('#travelExpense').prepend($expensesPaidByOpit);

  $('#travelExpense').prepend($expensesPaidByMe);

  $('#travelExpense').prepend($generalFormFieldset);

  $('#travelExpense').addClass('travelForm');

  $('.formFieldset').on('change', '.te-expense-type', function() {
    return $(this).closest('.formFieldsetChild').children('.selected-expense').html($("#" + ($(this).attr('id')) + " :selected").text());
  });

  $('.te-claim').each(function(index) {
    $(this).parent().addClass('inlineElements');
    $generalFormFieldset.append($(this).parent());
    if ($(this).hasClass('display-none')) {
      $(this).removeClass('display-none');
      $(this).parent().addClass('display-none');
    }
    if (index % 2) {
      return $generalFormFieldset.append($('<br>'));
    }
  });

  $addCompanyTagLink = $('<div class="addFormFieldsetChild formFieldsetButton"><i class="fa fa-plus-square"></i>Add company expense</div>');

  $addCompanyTagLink.on('click', function() {
    return addNewForm($('#travelExpense_companyPaidExpenses'), $('#travelExpense').children('.formFieldset:nth-child(3)'));
  });

  $addUserTagLink = $('<div class="addFormFieldsetChild formFieldsetButton"><i class="fa fa-plus-square"></i>Add user expense</div>');

  $addUserTagLink.on('click', function() {
    return addNewForm($('#travelExpense_userPaidExpenses'), $('#travelExpense').children('.formFieldset:nth-child(2)'));
  });

  $form = $('#travelExpenseForm');

  $.validator.addMethod('compare', function(value, element) {
    var arrival, arrivalDate, arrivalTimeHour, arrivalTimeMinute, departure, departureDate, departureTimeHour, departureTimeMinute;
    departureDate = $('#travelExpense_departureDateTime_date').val();
    arrivalDate = $('#travelExpense_arrivalDateTime_date').val();
    departureTimeHour = $('#travelExpense_departureDateTime_time_hour').val();
    arrivalTimeHour = $('#travelExpense_arrivalDateTime_time_hour').val();
    departureTimeMinute = $('#travelExpense_departureDateTime_time_minute').val();
    arrivalTimeMinute = $('#travelExpense_arrivalDateTime_time_minute').val();
    departure = departureDate + ' ' + departureTimeHour + ':' + departureTimeMinute;
    arrival = arrivalDate + ' ' + arrivalTimeHour + ':' + arrivalTimeMinute;
    departure = new Date(departure);
    arrival = new Date(arrival);
    $('#travelExpense_arrivalDateTime_time_minute').css({
      border: 'solid 1px rgb(170, 170, 170)'
    });
    return departure < arrival;
  }, 'Arrival date should not be smaller than departure date.');

  $form.validate({
    ignore: [],
    rules: {
      'travelExpense[arrivalDateTime][time][minute]': 'compare',
      'travelExpense[taxIdentification]': {
        maxlength: 11
      },
      'travelExpense[advancesPayback]': {
        digits: true
      },
      'travelExpense[toSettle]': {
        digits: true
      }
    }
  });

  $('#travelExpense_add_travel_expense').on('click', function() {
    event.preventDefault();
    if ($form.valid()) {
      console.log('valid');
      return $.ajax({
        method: 'POST',
        url: Routing.generate('OpitNotesTravelBundle_expense_show_details'),
        data: 'preview=1&' + $form.serialize()
      }).done(function(data) {
        var $preview;
        $preview = $('<div id="dialog-travelrequest-preview"></div>').html(data);
        return $preview.dialog({
          open: function() {
            return $('.ui-dialog-title').append('<i class="fa fa-list-alt"></i> Details');
          },
          close: function() {
            return $preview.dialog("destroy");
          },
          width: 550,
          maxHeight: $(window).outerHeight() - 100,
          modal: true,
          buttons: {
            Cancel: function() {
              $preview.dialog("destroy");
            },
            Save: function() {
              $form.submit();
              $preview.dialog("destroy");
            }
          }
        });
      }).fail(function() {
        return $('<div></div>').html('The travel expense could not be saved due to an error.').dialog({
          title: 'Error'
        });
      });
    }
  });

}).call(this);
