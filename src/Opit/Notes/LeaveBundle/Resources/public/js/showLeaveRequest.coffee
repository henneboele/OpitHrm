# Init leave history for travel requests
history = new StatusHistory('OpitNotesLeaveBundle_status_history')
do history.init

# Listener to set end date to start date if empty
$('form#leaveRequestForm').on 'focus', '.end-date', ->
    $startDateInput = $(@).closest('div').prev().find 'input.start-date'

    $(@).val $startDateInput.val() if $(@).val() is ''

# Check the leave dates overlapping
$('form#leaveRequestForm').on 'change', '.start-date', ->
    checkDatesOverlapping $(@)

$('form#leaveRequestForm').on 'change', '.end-date', ->
    checkDatesOverlapping $(@)

$('form#leaveRequestForm .option-list-scrollable').mCustomScrollbar()

createErrorLabel = (errorMessage, errorClass = '', attributes = []) ->
    $errorLabel = $('<label>')
    $errorLabel
        .addClass 'error'
        .addClass errorClass
        .html errorMessage
        
    attributes.forEach (attribute) ->
        $errorLabel.attr attribute['property'], attribute['value']

    return $errorLabel

# Cechking dates overlapping on the current date input field.
checkDatesOverlapping = ($self) ->
    $formFieldset = $self.closest('.formFieldsetChild')
    $currentStartDate = $formFieldset.find '.start-date'
    $currentEndDate = $formFieldset.find '.end-date'
    # Call the validator to check dates overlapping.
    validateDatesOverlapping $currentStartDate, $currentEndDate, $self

# Validate the leave dates overlapping
validateDatesOverlapping = ($currentStartDate, $currentEndDate, $self) ->
    isValid = true

    # Iterate the leave requests
    $('.formFieldsetChild').each (index, element) ->
        $startDate = $(element).find '.start-date'
        $endDate = $(element).find '.end-date'

        # Check DOMs are notthe same (checking the DOM level) in order to avoid to compoare itself values.
        # Compare the Dom's first element show the level. Otherwise it will not work.
        if $startDate[0] != $currentStartDate[0]
            # Check dates overlapping.
            if ($currentStartDate.val() <= $endDate.val()) and ($startDate.val() <= $currentEndDate.val())
                # Check there is an error class or not
                # Prevent to add more error class on the element
                if $self.hasClass('error') is no
                    $self.addClass 'error'
                    $self.parent().append createErrorLabel(
                        'Dates are overlapping: ' + $startDate.val() + ' and ' + $endDate.val(),
                        'overlap-error',
                        [{'property': 'data-start-date', 'value': $startDate.val()}, {'property': 'data-end-date', 'value': $endDate.val()}]
                    )
                else
                    $errorLabel = $self.parent().find 'label.overlap-error'
                    # Refresh the dates of error message
                    if ($errorLabel.data 'start-date' != $startDate) and ($errorLabel.data 'end-date' != $endDate)
                        $errorLabel.html 'Date overlapping with: ' + $startDate.val() + ' ' + $endDate.val()
                isValid = false
                # Breaking out the loop!
                # In jQuery it breaks the loop and not go out from the method!
                return false
            else
                # If there is not overlapping remove the error class
                $self.removeClass 'error'
                $self.parent().find('label.overlap-error').remove()
    return isValid

$(document).ready ->
    compareLeaveDates = () ->
        isValid = yes
        $('.formFieldsetChild').each (index) ->
            $startDate = $(@).find('.start-date')
            $startDateParent = $startDate.parent()
            startDateVal = $startDate.val()
            
            $endDate = $(@).find('.end-date')
            endDateVal = $endDate.val()
            
            if startDateVal > endDateVal
                isValid = no
                if $startDateParent.children('label.bigger-error').length is 0
                    $startDateParent.append createErrorLabel('Start date can not be bigger than end date.', 'bigger-error')
                    $startDate.addClass 'error'
            else
                $startDateParent.find('label.bigger-error').remove()
                if $startDateParent.find('label.past-error').length == 0
                    $startDate.removeClass 'error'
            
        return isValid
        
    validateNumberOfLeaves = () ->
        isValid = yes
        if $('.formFieldsetChild').length <= 0
            isValid = no
            if $('.leave-error').length <= 0
                $errorContainer = $('#reply-message')
                $errorMessage = $('<ul>').addClass('leave-error').append $('<li>').html('No leave date added.')
                $errorContainer
                    .append $errorMessage
                    .removeClass 'display-none'
        else
            $('#reply-message').addClass 'display-none'
            $('.leave-error').remove()
            
        return isValid
        
    validatePastDates = () ->
        isValid = yes
        $('.start-date').each () ->
            $startDate = $(@)
            $startDateParent = $startDate.parent()
            
            dateNow = new Date()
            startDate = new Date($startDate.val())
            
            if startDate < dateNow
                isValid = no
                if $startDateParent.children('label.past-error').length is 0
                    $startDateParent.append createErrorLabel('Start date can not be in the past.', 'past-error')
                    $startDate.addClass 'error'
            else
                $startDateParent.find('label.past-error').remove()
                $startDate.removeClass 'error'
                
        return isValid
        
    validateGm = () ->
        isValid = yes
        $generalManger = $('#leave_request_general_manager')
        $generalManagerAc = $('#leave_request_general_manager_ac')
        if $generalManger.val() == ''
            $generalManagerAc.parent().append createErrorLabel('A general manager must be selected.', 'gm-error')
            $generalManagerAc.addClass 'error'
            isValid = no
        else
            $generalManagerAc.parent().find('.gm-error').remove()
            $generalManagerAc.removeClass 'error'
            
        return isValid
            
    # method to create button to delete a leave
    createLeaveDeleteButton = () ->
        $deleteButtonWrapper = $('<div>')
            .addClass 'deleteFormFieldsetChild formFieldsetButton form-fieldset-delete-button'
            .html '<i class="fa fa-minus-square"></i>Delete'
            .on 'click', ->
                $(@).closest('.formFieldsetChild').remove()
            
        return $deleteButtonWrapper
        
    # function to create and insert a leave into the interface
    createLeave = ($leave) ->
        if typeof $leave is 'object'
            $leave.find('.start-date').parent().addClass 'display-inline-block'
            $leave.find('.end-date').parent().addClass 'display-inline-block margin-left-5'
            
        index = $collectionHolder.data 'index'
        $collectionHolder.data('index', index + 1)
        $leaveWrapper = $('<div>').addClass 'formFieldsetChild padding-10 margin-left-1-em margin-bottom-1-em display-inline-block vertical-align-top'
        if $leave is undefined
            $leave = $(prototype.replace /__name__/g, index)
            $leaveWrapper.append $leave

            # init datepicker plugin
            $(document).data('notes').funcs.initDateInputs $leave
        else
            $leaveWrapper.append $leave

        $leave.append createLeaveDeleteButton()
        
        $errorList = $leave.find('ul')
        $errorListParent = $errorList.parent()
        if $errorList.length > 0
            $errorListParent.append $('<label>').addClass('error').html($errorList.find('li:first').html())
            $input = $errorListParent.find 'input'
            $input.addClass 'error'
            
            $errorList.remove()

        $leave.find('.description').removeAttr 'required'
    
        $leaveWrapper.insertBefore $('.addFormFieldsetChild')
        return $leave

    $('.changeState').on 'change', ->
        $(document).data('notes').funcs.changeStateDialog $(@), $(document).data('notes').funcs.changeLeaveRequestStatus, $(@).data('lr'), 'leave'
        
    $('#leave_request_team_manager_ac').autocomplete
        source: (request, response) ->
            $.post Routing.generate('OpitNotesUserBundle_user_search', role: 'role_team_manager'), request, (data) -> response(data)
        minLength: 2
        select: (event, ui) ->
            $('#leave_request_team_manager').val ui.item.id
            return

    $('#leave_request_general_manager_ac').autocomplete
        source: (request, response) ->
            $.post Routing.generate('OpitNotesUserBundle_user_search', role: 'role_general_manager'), request, (data) -> response(data)
        minLength: 2
        select: (event, ui) ->
            $('#leave_request_general_manager').val ui.item.id
            return
            
    $forAll = $('#forAll')
    $companyEmployees = $('.company-employees')
    
    changeLabel = (list) ->
        if $(list).filter(':checked').length is list.length
            $forAll.html('Uncheck all')
        else
            $forAll.html('Check all')
    
    $forAll.on 'click', ->
        $companyEmployees.checkAll (list) =>
            changeLabel list
            
    $companyEmployees.on 'change', ->
        changeLabel $companyEmployees

    $('#leave_request').find('label:first').remove()
    $collectionHolder = $('#leave_request_leaves')
    $collectionHolder.data 'index', 0
    
    prototype = $collectionHolder.data 'prototype'
    $prototype = $(prototype)
    $prototype.find('.start-date').parent().addClass('display-inline-block')
    $prototype.find('.end-date').parent().addClass('display-inline-block margin-left-5')
    prototype = $prototype.html().replace '<label class="required">__name__label__</label>', ''
    
    $form = $collectionHolder.closest 'form'
    $form.prepend $('.formFieldset')
    $form.find('#leave_request_create_leave_request').parent().append $('#cancel-button')

    $collectionHolder.children().each (index) ->
        $(@).find('label:first').remove()
        createLeave($(@))
    
    $('.addFormFieldsetChild').on 'click', ->    
        createLeave()
        
    $('.disabled .deleteFormFieldsetChild, .disabled .addFormFieldsetChild').each ->
        $(@).remove()
        
    $('.disabled select, .disabled input, .disabled textarea').each ->
        $(@).attr 'disabled', 'disabled'
        
    $('.disabled #leave_request_create_leave_request')
        .addClass 'button-disabled'
        .attr 'disabled', 'disabled'
        
    $('.disabled #leave_request_create_leave_request').attr 'disabled', 'disabled'

    $leaveRequestUser = $('#leave_request_user_ac')
    $addFormFieldset = $('.addFormFieldsetChild')
    $employeeSelector = $('#employee-selector')
    $employeeSelector.addClass 'display-none-important'
    if $('#employee-selector').length != 0
        $leaveRequestUser.parent().addClass('display-inline-block display-none-important')
        $addFormFieldset.addClass 'display-none-important'

    $('.leave-request-owner').on 'change', ->
        $('.formFieldsetChild').remove()
        displayNone = 'display-none-important'
        if $(@).val() is 'for-employees'
            $leaveRequestUser.parent().addClass displayNone
            $addFormFieldset.addClass displayNone
            $employeeSelector.removeClass displayNone
            
            $employeeSelector.removeAttr 'disabled'

            $leave = createLeave()
            $leave.find('.deleteFormFieldsetChild').remove()
            $leave.find('.leave-category').parent().remove()
        else if $(@).val() is 'own'
            $employeeSelector.addClass displayNone
            $leaveRequestUser.parent().removeClass displayNone
            $addFormFieldset.removeClass displayNone
            
            $employeeSelector.attr 'disabled', 'disabled'
    
    $( '#leave_request_create_leave_request' ).on 'click', (event) ->
        event.preventDefault()
        if compareLeaveDates() is yes and validateNumberOfLeaves() is yes and validatePastDates() is yes and validateGm() is yes
            $('#leaveRequestForm').submit()
            return
        