// Generated by CoffeeScript 1.7.1
(function() {
  var $addCompanyTagLink, $addUserTagLink, $expensesPaidByMe, $expensesPaidByMeDesc, $expensesPaidByOpit, $expensesPaidByOpitDesc, $form, $formFieldset, $generalFormFields, $generalFormFieldset, $perDiem, addNewAdvanceReceived, addNewForm, allCurrencies, availableCurrencies, calculateAdvancesPayback, calculatePerDiem, convertCurrency, createCustomField, createDeleteButton, createDeleteExpenseButton, createTableRow, excludedCurrencies, expenseDateChange, reCreateAdvances, reCreateExpenses, setAvailableCurrencies, setCurrenciesArray, validateAllExpenseDates, validateExpenseDate;

  $('.travel-status-history').click(function(event) {
    var id;
    event.preventDefault();
    id = $(this).data('id');
    return $(document).data('notes').funcs.showTravelStatusHistory(id, 'te');
  });

  calculateAdvancesPayback = function() {
    var amount, spent;
    spent = [];
    amount = 0;
    $('.elementContainer .currency').each(function() {
      var $amountEl;
      $amountEl = $(this).closest('.formFieldsetChild').find('.amount');
      amount = parseInt($amountEl.val());
      if (spent[$(this).val()] === void 0) {
        spent[$(this).val()] = amount;
      } else {
        spent[$(this).val()] += amount;
      }
      if (isNaN(spent[$(this).val()])) {
        return console.warn("Value is not a number (" + ($amountEl.attr('id')) + ")");
      }
    });
    return $('.generalFormFieldset .te-advances-received-currency').each(function() {
      var $advancesSpent, $closestAdvancesReceived, advancePayBack, advancesReceived, advancesSpent;
      $closestAdvancesReceived = $(this).closest('.advances-received');
      advancesSpent = spent[$(this).val()];
      $advancesSpent = $closestAdvancesReceived.find('.te-advances-spent');
      advancesReceived = $closestAdvancesReceived.find('.te-advances-received').val();
      advancePayBack = parseInt(advancesReceived - parseInt(advancesSpent));
      $advancesSpent.html(advancesSpent === void 0 ? '0' : isNaN(advancesSpent) ? '0' : advancesSpent);
      return $closestAdvancesReceived.find('.te-advances-payback').html(advancePayBack ? advancePayBack < 0 ? '0' : advancePayBack : isNaN(advancePayBack) ? advancesReceived === '' ? '0' : advancesReceived : '0');
    });
  };

  excludedCurrencies = [];

  allCurrencies = [];

  availableCurrencies = [];

  setCurrenciesArray = function(arrayToPushTo) {
    return $($('#travelExpense_advancesReceived').data('prototype')).find('option').each(function() {
      return arrayToPushTo.push($(this).val());
    });
  };

  setAvailableCurrencies = function() {
    var currency, _i, _len;
    excludedCurrencies = [];
    availableCurrencies = [];
    if ($('.te-advances-received-currency').length === 0) {
      setCurrenciesArray(availableCurrencies);
    } else {
      $('.te-advances-received-currency').each(function() {
        return $(this).find('option').each(function() {
          if ($(this).prop('selected')) {
            return excludedCurrencies.push($(this).val());
          } else {
            return $(this).remove();
          }
        });
      });
    }
    for (_i = 0, _len = allCurrencies.length; _i < _len; _i++) {
      currency = allCurrencies[_i];
      if ($.inArray(currency, excludedCurrencies) <= -1) {
        availableCurrencies.push(currency);
        $('.te-advances-received-currency').append($('<option>').html(currency).attr('value', currency));
      }
    }
    return calculateAdvancesPayback();
  };

  createDeleteButton = function() {
    var $deleteButton;
    $deleteButton = $('<div>');
    $deleteButton.addClass('deleteFormFieldsetChild formFieldsetButton').html('<i class="fa fa-minus-square"></i>Delete');
    $deleteButton.on('click', function() {
      $(this).parent().remove();
      return calculateAdvancesPayback();
    });
    return $deleteButton;
  };

  createDeleteExpenseButton = function($parent) {
    var $deleteButton, $inlineElement;
    $inlineElement = $('<div>');
    $inlineElement.addClass('inlineElements');
    $deleteButton = $('<i>');
    $deleteButton.addClass('fa fa-minus-square color-red hover-cursor-pointer margin-top-24');
    $deleteButton.on('click', function() {
      $(this).closest('.advances-received').remove();
      setAvailableCurrencies();
      return calculateAdvancesPayback();
    });
    $inlineElement.append($deleteButton);
    return $parent.append($inlineElement);
  };

  validateAllExpenseDates = function() {
    var $formFieldsetChilds, isDateValid;
    isDateValid = true;
    $formFieldsetChilds = $('.formFieldsetChild');
    $formFieldsetChilds.each(function() {
      var expenseDateField;
      expenseDateField = $(this).find('input[type=date]');
      validateExpenseDate(expenseDateField);
      if (expenseDateField.parent().children('.custom-error').length > 0) {
        isDateValid = false;
      }
    });
    return isDateValid;
  };

  validateExpenseDate = function(self) {
    var $errorLabel, arrivalDate, date, departureDate, isDateValid;
    isDateValid = true;
    date = self.val();
    self.addClass('display-inline-block');
    departureDate = $('#travelExpense_departureDateTime_date').val();
    arrivalDate = $('#travelExpense_arrivalDateTime_date').val();
    if (date > arrivalDate || date < departureDate) {
      if (self.parent().children('.custom-error').length < 1) {
        $errorLabel = $('<label>');
        $errorLabel.addClass('custom-error');
        $errorLabel.text('Invalid expense date.');
        return self.parent().append($errorLabel);
      }
    } else {
      return self.parent().children().remove('.custom-error');
    }
  };

  expenseDateChange = function(parent) {
    var $dateOfExpenseSpent;
    $dateOfExpenseSpent = parent.find('input[type=date]');
    if ($dateOfExpenseSpent.attr('id').indexOf('userPaidExpenses') > -1) {
      return $dateOfExpenseSpent.on('change', function() {
        return validateExpenseDate($(this));
      });
    }
  };

  reCreateExpenses = function(self) {
    var $container, $selectedExpense;
    $selectedExpense = $('<span>').addClass('selected-expense');
    $selectedExpense.html(self.find('.te-expense-type').find(':selected').text());
    $container = $('<div>').addClass('formFieldsetChild');
    self.children('label:first').remove();
    $container.append(self);
    $container.append(createDeleteButton());
    $container.prepend($selectedExpense);
    $container.find('.amount').on('change', function() {
      return calculateAdvancesPayback();
    });
    $container.find('.currency').on('change', function() {
      return calculateAdvancesPayback();
    });
    expenseDateChange($container);
    return $container;
  };

  createCustomField = function(className, labelText, content) {
    var $customField, $customFieldInline;
    $customFieldInline = $('<div>');
    $customFieldInline.addClass('inlineElements');
    $customField = $('<div>');
    $customField.html(content);
    $customField.addClass(className);
    $customFieldInline.append($('<label>').html(labelText));
    $customFieldInline.append($customField);
    return $customFieldInline;
  };

  reCreateAdvances = function() {
    var $generalFormFieldset, $teAdvancesReceived, collectionIndex;
    collectionIndex = 0;
    $teAdvancesReceived = $('#travelExpense_advancesReceived');
    $generalFormFieldset = $('.generalFormFieldset');
    $teAdvancesReceived.parent().children('label').remove();
    $teAdvancesReceived.children().each(function() {
      return $(this).find('label').remove();
    });
    $('.te-advances-received').parent().addClass('inlineElements');
    $('.te-advances-received-currency').parent().addClass('inlineElements');
    $('.te-advances-received-currency').parent().prepend($('<label>').html('Currency'));
    $('.te-advances-received').each(function(index) {
      var $advancesPayback, $advancesReceived, $advancesSpent, $selfParent, $teAdvances;
      $selfParent = $(this).parent();
      $selfParent.prepend($('<label>').html('Advances received'));
      $advancesPayback = createCustomField('te-advances-payback custom-field', 'Advances payback', '0');
      $advancesSpent = createCustomField('te-advances-spent custom-field', 'Advances spent', '0');
      $selfParent.after($advancesSpent);
      $advancesSpent.after($advancesPayback);
      collectionIndex++;
      $teAdvances = $('#travelExpense_advancesReceived_' + index);
      $advancesReceived = $('<div>');
      $advancesReceived.addClass('advances-received');
      $advancesReceived.append($teAdvances);
      $generalFormFieldset.append($advancesReceived);
      return createDeleteExpenseButton($teAdvances);
    });
    $teAdvancesReceived.data('index', collectionIndex);
    $generalFormFieldset.on('change', '.te-advances-received', function() {
      return calculateAdvancesPayback();
    });
    $generalFormFieldset.on('change', '.te-advances-received-currency', function() {
      return calculateAdvancesPayback();
    });
    return calculateAdvancesPayback();
  };

  addNewAdvanceReceived = function(collectionHolder) {
    var $advancesPayback, $advancesSpent, $defaultOption, $newAdvancesReceived, index, newAdvancesReceived, prototype;
    if (availableCurrencies.length > 0) {
      prototype = collectionHolder.data('prototype');
      index = collectionHolder.data('index');
      prototype = prototype.replace('<label class="required">__name__label__</label>', '');
      newAdvancesReceived = prototype.replace(/__name__/g, index);
      $newAdvancesReceived = $(newAdvancesReceived);
      $newAdvancesReceived.addClass('advances-received');
      $newAdvancesReceived.children('div').children('div').each(function() {
        return $(this).addClass('inlineElements');
      });
      $advancesPayback = createCustomField('te-advances-payback custom-field', 'Advances payback', '0');
      $advancesSpent = createCustomField('te-advances-spent custom-field', 'Advances spent', '0');
      $newAdvancesReceived.find('.te-advances-received').parent().after($advancesSpent);
      $advancesSpent.after($advancesPayback);
      createDeleteExpenseButton($newAdvancesReceived.children('div'));
      $('.generalFormFieldset .addFormFieldsetChild').before($newAdvancesReceived);
      collectionHolder.data('index', index + 1);
      if (allCurrencies.length === 0) {
        setCurrenciesArray(allCurrencies);
      }
      $newAdvancesReceived.find('.te-advances-received-currency').find('option').remove();
      $defaultOption = $('<option>');
      $defaultOption.html(availableCurrencies[0]);
      $defaultOption.val(availableCurrencies[0]);
      $newAdvancesReceived.find('.te-advances-received-currency').append($defaultOption);
      setAvailableCurrencies();
      return calculateAdvancesPayback();
    } else {
      if (setCurrenciesArray(availableCurrencies) === true) {
        if (availableCurrencies.length !== $('.te-advances-received-currency').length) {
          return addNewAdvanceReceived(collectionHolder);
        } else {
          return availableCurrencies = [];
        }
      }
    }
  };

  addNewForm = function(collectionHolder, parent) {
    var $datePicker, $formFieldsetChild, $selectedExpense, id, index, name, newForm, prototype;
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
    expenseDateChange($($formFieldsetChild));
    $formFieldsetChild.find('.currency option[value=EUR]').attr('selected', 'selected');
    collectionHolder.data('index', index + 1);
    $formFieldsetChild.find('.amount').on('change', function() {
      return calculateAdvancesPayback();
    });
    $formFieldsetChild.find('.currency').on('change', function() {
      return calculateAdvancesPayback();
    });
    if (!Modernizr.inputtypes.date) {
      $datePicker = $formFieldsetChild.find('input[type=date]');
      id = $datePicker.attr('id');
      name = $datePicker.attr('name');
      $datePicker.after('<input type="hidden" name="' + name + '" id="altDate' + id + '" />');
      $datePicker.datepicker({
        altField: '#altDate' + id,
        altFormat: 'yy-mm-dd'
      });
    }
    return parent.find('.addFormFieldsetChild').before($formFieldsetChild);
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

  $perDiem = $('<div>').addClass('display-inline-block vertical-align-top per-diem-details-wrapper');

  convertCurrency = function(originCode, destinationCode, value) {
    if (originCode === destinationCode) {
      return value;
    } else {
      return curConverter.convertCurrency(originCode, destinationCode, value).toFixed(2);
    }
  };

  calculatePerDiem = function(departureDate, departureHour, departureMinute, arrivalDate, arrivalHour, arrivalMinute) {
    var arrival, departure;
    departure = new Date("" + departureDate + " " + departureHour + ":" + departureMinute);
    arrival = new Date("" + arrivalDate + " " + arrivalHour + ":" + arrivalMinute);
    if (arrival > departure) {
      return $.ajax({
        method: 'POST',
        url: Routing.generate('OpitNotesTravelBundle_expense_perdiem'),
        data: {
          arrival: arrival,
          departure: departure
        }
      }).done(function(data) {
        var $perDiemTable;
        $('.perDiemTable').remove();
        $perDiemTable = $('<table>');
        $perDiemTable.addClass('perDiemTable bordered');
        if (data['totalTravelHoursOnSameDay'] > 0) {
          $perDiemTable.append(createTableRow('One day trip', data['totalPerDiem'], "Hours traveled " + data['totalTravelHoursOnSameDay'] + "."));
        } else {
          $perDiemTable.append(createTableRow('Departure day', data['departurePerDiem'], "Hours traveled on departure day " + data['departureHours'] + "."));
          $perDiemTable.append(createTableRow("Full days (" + data['daysBetween'] + ")", data['daysBetweenPerDiem'], "Number of full days " + data['daysBetween'] + "."));
          $perDiemTable.append(createTableRow('Arrival day', data['arrivalPerDiem'], "Hours traveled on arrival day " + data['arrivalHours'] + "."));
          $perDiemTable.append(createTableRow('Total', data['totalPerDiem']));
        }
        return $perDiem.append($perDiemTable);
      });
    }
  };

  convertCurrency = function(originCode, destinationCode, value) {
    return curConverter.convertCurrency(originCode, destinationCode, value);
  };

  $(document).ready(function() {
    var $addNewAdvance, $advancesReceived, $arrivalHour, $arrivalMinute, $buttonParent, $departureHour, $departureMinute, $perDiemAmountsTable, $secondFormFieldset, $td, $thirdFormFieldset, $tr, arrivalDate, arrivalDateVal, arrivalHourVal, arrivalMinuteVal, arrivalTime, companyPaidExpensesIndex, departureDate, departureDateVal, departureHourVal, departureMinuteVal, departureTime, userPaidExpensesIndex;
    setCurrenciesArray(allCurrencies);
    setAvailableCurrencies();
    $buttonParent = $('#travelExpense_add_travel_expense').parent();
    $(document).data('notes').funcs.createButton('Cancel', 'button display-inline-block', '', $buttonParent, 'OpitNotesTravelBundle_travel_list');
    $(document).data('notes').funcs.makeElementToggleAble('h3', $('.formFieldset'), '.elementContainer');
    arrivalDate = $('#travelExpense_arrivalDateTime_date');
    arrivalTime = $('#travelExpense_arrivalDateTime_time');
    departureDate = $('#travelExpense_departureDateTime_date');
    departureTime = $('#travelExpense_departureDateTime_time');
    if (!Modernizr.inputtypes.date) {
      arrivalDate.datepicker('destroy');
      departureDate.datepicker('destroy');
      $('input[type=date]').each(function() {
        var dateVal;
        dateVal = $(this).val();
        return $(this).val($(this).val().replace(/(\d{4})-(\d{2})-(\d{2})/, "$2/$3/$1"));
      });
    } else {
      arrivalDate.attr('readonly', 'readonly');
      departureDate.attr('readonly', 'readonly');
    }
    arrivalTime.addClass('inlineElements time-picker');
    departureTime.addClass('inlineElements time-picker');
    arrivalDate.css({
      display: 'inline-block'
    });
    departureDate.css({
      display: 'inline-block'
    });
    $secondFormFieldset = $('#travelExpense').children('.formFieldset:nth-child(2)');
    $thirdFormFieldset = $('#travelExpense').children('.formFieldset:nth-child(3)');
    $secondFormFieldset.append($('<div>').addClass('elementContainer'));
    $thirdFormFieldset.append($('<div>').addClass('elementContainer'));
    $secondFormFieldset.find('.elementContainer').append($addUserTagLink);
    $thirdFormFieldset.find('.elementContainer').append($addCompanyTagLink);
    companyPaidExpensesIndex = 0;
    userPaidExpensesIndex = 0;
    if ($('#travelExpense_companyPaidExpenses').children('div').length > 0) {
      $('#travelExpense_companyPaidExpenses').children('div').each(function() {
        var $container;
        $container = reCreateExpenses($(this));
        $('#travelExpense').children('.formFieldset:nth-child(3)').find('.addFormFieldsetChild').before($container);
        return companyPaidExpensesIndex++;
      });
    }
    if ($('#travelExpense_userPaidExpenses').children('div').length > 0) {
      $('#travelExpense_userPaidExpenses').children('div').each(function() {
        var $container;
        $container = reCreateExpenses($(this));
        $('#travelExpense').children('.formFieldset:nth-child(2)').find('.addFormFieldsetChild').before($container);
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
    $perDiemAmountsTable.addClass('per-diem-amounts-slab bordered');
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
    $td.html('Per diem is calculated considering the following slab.');
    $tr.append($td);
    $perDiemAmountsTable.prepend($tr);
    $perDiem.append($perDiemAmountsTable);
    $('.generalFormFieldset').find('br').last().remove();
    $perDiem.append($perDiemAmountsTable);
    $('.generalFormFieldset').append($perDiem);
    $('.fa-question-circle').on('mouseover', function() {
      var $description;
      $description = $(this).parent().parent().find('.formFieldsetDescription');
      return $description.removeClass('display-none');
    });
    $('.fa-question-circle').on('mouseout', function() {
      return $('.formFieldsetDescription').addClass('display-none');
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
    if (!isNewTravelExpense) {
      calculatePerDiem(departureDateVal, departureHourVal, departureMinuteVal, arrivalDateVal, arrivalHourVal, arrivalMinuteVal);
    }
    $departureHour.on('change', function() {
      departureHourVal = $departureHour.val();
      return calculatePerDiem(departureDateVal, departureHourVal, departureMinuteVal, arrivalDateVal, arrivalHourVal, arrivalMinuteVal);
    });
    $departureMinute.on('change', function() {
      departureMinuteVal = $departureMinute.val();
      return calculatePerDiem(departureDateVal, departureHourVal, departureMinuteVal, arrivalDateVal, arrivalHourVal, arrivalMinuteVal);
    });
    $arrivalHour.on('change', function() {
      arrivalHourVal = $arrivalHour.val();
      return calculatePerDiem(departureDateVal, departureHourVal, departureMinuteVal, arrivalDateVal, arrivalHourVal, arrivalMinuteVal);
    });
    $arrivalMinute.on('change', function() {
      arrivalMinuteVal = $arrivalMinute.val();
      return calculatePerDiem(departureDateVal, departureHourVal, departureMinuteVal, arrivalDateVal, arrivalHourVal, arrivalMinuteVal);
    });
    reCreateAdvances();
    $advancesReceived = $('#travelExpense_advancesReceived');
    $addNewAdvance = $('<div>');
    $addNewAdvance.addClass('addFormFieldsetChild formFieldsetButton margin-left-0');
    $addNewAdvance.html('<i class="fa fa-plus-square"></i>Add advances received');
    $('.generalFormFieldset').append($addNewAdvance);
    $addNewAdvance.on('click', function() {
      return addNewAdvanceReceived($advancesReceived);
    });
    $('.generalFormFieldset').on('change', '.te-advances-received-currency', function() {
      return setAvailableCurrencies();
    });
    if ($('#travelExpense_add_travel_expense').hasClass('button-disabled')) {
      $('.addFormFieldsetChild').each(function() {
        return $(this).remove();
      });
      $('.deleteFormFieldsetChild').each(function() {
        return $(this).remove();
      });
      $('.fa-minus-square').each(function() {
        return $(this).remove();
      });
    }
    return $('.changeState').on('change', function() {
      var statusId, travelExpenseId;
      statusId = $(this).val();
      travelExpenseId = $(this).data('te');
      return $(document).data('notes').funcs.changeStateDialog($(this), $(document).data('notes').funcs.changeTravelExpenseStatus, travelExpenseId);
    });
  });

  $formFieldset = $('<div>');

  $formFieldset.addClass('formFieldset');

  $generalFormFieldset = $formFieldset.clone().addClass('generalFormFieldset clearfix');

  $expensesPaidByMe = $formFieldset.clone().append($('<h3>').html('Expenses paid by me <i class="fa fa-question-circle"></i>'));

  $expensesPaidByOpit = $formFieldset.clone().append($('<h3>').html('Expenses paid by opit <i class="fa fa-question-circle"></i>'));

  $('#travelExpense').prepend($expensesPaidByOpit);

  $('#travelExpense').prepend($expensesPaidByMe);

  $('#travelExpense').prepend($generalFormFieldset);

  $('#travelExpense').addClass('travelForm');

  $generalFormFields = $('<div>').addClass('display-inline-block');

  $generalFormFieldset.append($generalFormFields);

  $expensesPaidByOpitDesc = $('<div>');

  $expensesPaidByOpitDesc.html('Expenses paid by OPIT (already paid by OPIT).');

  $expensesPaidByOpitDesc.addClass('formFieldsetDescription short-description display-none');

  $expensesPaidByMeDesc = $('<div>');

  $expensesPaidByMeDesc.html('Expenses paid by employee (payable to your own bank account).');

  $expensesPaidByMeDesc.addClass('formFieldsetDescription short-description display-none');

  $expensesPaidByOpit.append($expensesPaidByOpitDesc);

  $expensesPaidByMe.append($expensesPaidByMeDesc);

  $('.formFieldset').on('change', '.te-expense-type', function() {
    return $(this).closest('.formFieldsetChild').children('.selected-expense').html($("#" + ($(this).attr('id')) + " :selected").text());
  });

  $('.te-claim').each(function(index) {
    $(this).parent().addClass('inlineElements');
    $generalFormFields.append($(this).parent());
    if ($(this).hasClass('display-none')) {
      $(this).removeClass('display-none');
      $(this).parent().addClass('display-none');
    }
    if (index % 2) {
      return $generalFormFields.append($('<br>'));
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
    if (departureDate.indexOf('-')) {
      arrivalDate = arrivalDate.replace(/(\d{4})-(\d{2})-(\d{2})/, "$2/$3/$1");
      departureDate = departureDate.replace(/(\d{4})-(\d{2})-(\d{2})/, "$2/$3/$1");
    }
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
  }, 'Arrival date should not be earlier than departure date.');

  $form.validate({
    ignore: [],
    rules: {
      'travelExpense[arrivalDateTime][time][minute]': 'compare',
      'travelExpense[taxIdentification]': {
        maxlength: 11
      },
      'travelExpense[toSettle]': {
        digits: true
      }
    }
  });

  $('#travelExpense_add_travel_expense').on('click', function(event) {
    event.preventDefault();
    if (!$(this).hasClass('button-disabled')) {
      if ($form.valid() && validateAllExpenseDates()) {
        if (!Modernizr.inputtypes.date) {
          $('input[type=date]').each(function() {
            return $(this).parent().find('input[type=hidden]').val($(this).val().replace(/(\d{2})\/(\d{2})\/(\d{4})/, "$3-$1-$2"));
          });
        }
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
    }
  });

  $('#travelRequestPreview').on('click', function() {
    $.ajax({
      method: 'POST',
      url: Routing.generate('OpitNotesTravelBundle_travel_show_details'),
      data: {
        'id': $(this).attr('data-tr-id')
      }
    }).done(function(data) {
      var $previewTr;
      $previewTr = $('<div id="dialog-show-details-tr"></div>');
      $previewTr.html(data).dialog({
        open: function() {
          return $('.ui-dialog-title').append('<i class="fa fa-list-alt"></i> Details');
        },
        close: function() {
          return $previewTr.dialog("destroy");
        },
        width: 550,
        maxHeight: $(window).outerHeight() - 100,
        modal: true
      });
    });
  });

}).call(this);
