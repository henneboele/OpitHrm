# Where addClass called twice first addClass contains selector(s) and second the actual styles

addFormDeleteButton = ->
    $deleteButton = $('<div>').html '<i class="fa fa-minus-square"></i>Delete'
    $deleteButton.addClass 'deleteFormFieldsetChild formFieldsetButton'
    $deleteButton.addClass 'form-fieldset-delete-button'
    $deleteButton.click ->
        $(@).parent().remove()
    return $deleteButton
    
numberOfNightsListener = (form) ->
        form.find('input[type=number]').first().addClass 'number-of-nights'
        form.find('input[type=number]').first().on 'change', ->
            compareDays()

$('.travel-status-history').click (event) ->
    event.preventDefault()
    id = $(@).data 'id'
    $(document).data('notes').funcs.showTravelStatusHistory id, 'tr'

# custom compare validator to compare length of trip and number of nights
compareDays = () ->
    #get arrival and departure date value
    departureDate = new Date $('#travelRequest_departure_date').val()
    arrivalDate = new Date $('#travelRequest_arrival_date').val()
    
    #get days between arrival and departure
    diffDays = (arrivalDate.getTime() - departureDate.getTime())/(24*60*60*1000)#one day
    accomodationDays = '0'
    # get all days from all accomodations
    $('#travelRequest_accomodations').find('.number-of-nights').each ->
        accomodationDays = parseInt($(@).val()) + parseInt(accomodationDays)
    $accomodationWrapper = $('#travelRequest_accomodations')
    
    # check if trip is longer than accomodations
    # and check if the diffDays is a positive value becasue to prevent that case
    # when the difference day between the departure and arrival dates is negative value
    # and there are no any filled up accommodations then this validation will not run.
    if diffDays > 0 and accomodationDays > diffDays
        if $accomodationWrapper.children('label.error-label').length is 0
            $errorMessage = $('<label>').html('Total accomodation duration can not exceed travel request duration.').addClass 'error'
            $accomodationWrapper.prepend '<br />'
            $accomodationWrapper.prepend $errorMessage
        return false
    else
        $accomodationWrapper.children('label.error-label').remove()
        $accomodationWrapper.children('br').remove()
        return true

$(document).ready ->
    $buttonParent = $('#travelRequest_add_travel_request').parent()
    $(document).data('notes').funcs.createButton 'Cancel', 'button display-inline-block', '', $buttonParent, 'OpitNotesTravelBundle_travel_list'
    $(document).data('notes').funcs.makeElementToggleAble 'h3', $('.formFieldset')

    $('#travelRequest').css display: 'block'
    
    $('.disabled input').each ->
        $(@).attr 'disabled', 'disabled'
    $('.disabled select').each ->
        $(@).attr 'disabled', 'disabled'
    $('.disabled button').each ->
        $(@).attr 'disabled', 'disabled'
        $(@).addClass 'button-disabled'
        
    if $('#travelRequest_add_travel_request').attr 'disabled'
        $('.addFormFieldsetChild').each ->
            $(@).remove()
        $('.deleteFormFieldsetChild').each ->
            $(@).remove()
    
    $('.changeState').on 'change', ->
        statusId = $(@).val()
        travelRequestId = $(@).data 'tr'
        $(document).data('notes').funcs.changeStateDialog $(@), $(document).data('notes').funcs.changeTravelRequestStatus, travelRequestId
            
    if not Modernizr.inputtypes.date
        $arrivalDate = $('#travelRequest_arrival_date')
        $departureDate = $('#travelRequest_departure_date')
        $('#altDatetravelRequest_arrival_date').val $arrivalDate.val()
        $('#altDatetravelRequest_departure_date').val $departureDate.val()
        $arrivalDate.val $arrivalDate.val().replace(/(\d{4})-(\d{2})-(\d{2})/, "$2/$3/$1")
        $departureDate.val $departureDate.val().replace(/(\d{4})-(\d{2})-(\d{2})/, "$2/$3/$1")

$('label.required').each ->
    if $(@).text() is '0' then $(@).remove()
    return
    
generalData = $('<div>').addClass 'formFieldset generalFormFieldset'
generalData.addClass 'padding-bottom-5 margin-top-20 margin-bottom-20'
generalData.append($('#travelRequest_user_ac,
                      #travelRequest_departure_date,
                      #travelRequest_arrival_date,
                      #travelRequest_customer_related,
                      #travelRequest_customer_name,
                      #travelRequest_trip_purpose').parent())
$('#travelRequest').prepend generalData

#add team manager and general manager to formFieldset
requiredApprovals = $('<div>').addClass 'formFieldset'
requiredApprovals.addClass 'padding-bottom-5 margin-top-20 margin-bottom-20'
requiredApprovals.append $('<h3>').addClass('background-color-orange color-white padding-top-2 padding-bottom-2 padding-left-1-em').html 'Required approvals'
$div = $('<div>')
$div.addClass 'margin-left-1-em'
$div.append $('#travelRequest_team_manager_ac').parent()
$div.append $('#travelRequest_general_manager_ac').parent()
requiredApprovals.append $div
$('#travelRequest_general_manager').after requiredApprovals

#add form fieldset class to travelRequest_destinations and travelRequest_accomodations for easier formatting
$destinationParent = $('#travelRequest_destinations').parent()
$destinationParent.addClass 'formFieldset'
$destinationParent.addClass 'padding-bottom-5 margin-top-20 margin-bottom-20'

$accomodationParent = $('#travelRequest_accomodations').parent()
$accomodationParent.addClass 'formFieldset'
$accomodationParent.addClass 'padding-bottom-5 margin-top-20 margin-bottom-20'

#set elements to be inlined
$('#travelRequest_departure_date, #travelRequest_arrival_date,
   #travelRequest_customer_related, #travelRequest_customer_name,
   #travelRequest_team_manager_ac, #travelRequest_general_manager_ac').parent().addClass 'display-inline-block vertical-align-top margin-right-1-em'

#add br tag to break inline
$('#travelRequest_arrival_date').parent().after('<br />')

#change label tags to h3 tags
accomodationsLabel = $('#travelRequest_accomodations').parent().children 'label'
accomodationsLabel.replaceWith('<h3 class="background-color-orange color-white padding-top-2 padding-bottom-2 padding-left-1-em">'+accomodationsLabel.html()+'</h3>')

destinationsLabel = $('#travelRequest_destinations').parent().children 'label'
destinationsLabel.replaceWith('<h3 class="background-color-orange color-white padding-top-2 padding-bottom-2 padding-left-1-em">'+destinationsLabel.html()+'</h3>')

#check if accomodation or destination was set
#if there are more input type text fields than one some data was sent
travelRequestDestinations0 = $('#travelRequest_destinations_0')
if $('#travelRequest_destinations :input[type=text]').length > 1
    $('#travelRequest_destinations').children().each ->
        $(@).addClass 'formFieldsetChild'
        $(@).addClass 'padding-10 margin-left-1-em margin-bottom-1-em display-inline-block vertical-align-top'
        $(@).children().remove 'label'
        $(@).append addFormDeleteButton
        
        $currency = $(@).find('.currency')
        $cost = $(@).find('.cost')
        $cost.parent().append $currency       
        
#if there is only one input type text field check if it has got any value
else
    if $('#travelRequest_destinations :input[type=text]').val() is ""
        travelRequestDestinations0.parent().remove()
    else
        travelRequestDestinations0.parent().addClass 'formFieldsetChild'
        travelRequestDestinations0.parent().addClass 'padding-10 margin-left-1-em margin-bottom-1-em display-inline-block vertical-align-top'
        travelRequestDestinations0.parent().append addFormDeleteButton
        
        $currency = travelRequestDestinations0.find('.currency')
        $cost = travelRequestDestinations0.find('.cost')
        $cost.parent().append $currency        

travelRequestAccomodations0 = $('#travelRequest_accomodations_0')
if $('#travelRequest_accomodations :input[type=text]').length > 2
    $('#travelRequest_accomodations').children().each ->        
        numberOfNightsListener($(@))
        
        $(@).addClass 'formFieldsetChild'
        $(@).addClass 'padding-10 margin-left-1-em margin-bottom-1-em display-inline-block vertical-align-top'
        $(@).children().remove 'label'
        $(@).append addFormDeleteButton
        
        $currency = $(@).find('.currency')
        $cost = $(@).find('.cost')
        $cost.parent().append $currency       
else
    if $('#travelRequest_accomodations :input[type=text]').val() is ""
        travelRequestAccomodations0.parent().remove()
    else
        numberOfNightsListener(travelRequestAccomodations0.parent())
        
        travelRequestAccomodations0.parent().addClass 'formFieldsetChild'
        travelRequestAccomodations0.parent().addClass 'padding-10 margin-left-1-em margin-bottom-1-em display-inline-block vertical-align-top'
        travelRequestAccomodations0.parent().append addFormDeleteButton
        
        $currency = travelRequestAccomodations0.find('.currency')
        $cost = travelRequestAccomodations0.find('.cost')
        $cost.parent().append $currency       

#check customer related value
travelCustomer = $('#travelRequest_customer_name')
if travelCustomer.val() is ''
    travelCustomer.parent().css {visibility: 'hidden'}
else
    $('#travelRequest_customer_related').val('0')

$('#travelRequest_customer_related').change ->
    if $(@).val() is "0"
        travelCustomer.parent().css {visibility: 'visible'}
        travelCustomer.attr('required', 'required')
    else 
        travelCustomer.parent().css {visibility: 'hidden'}
        travelCustomer.removeAttr 'required'

$form = $('#travelRequestForm')

$('#travelRequest_user_ac').autocomplete
    source: Routing.generate 'OpitNotesTravelBundle_travel_userSearch', role: 'all'
    minLength: 2
    response: (event, ui) ->
        return  
    select: (event, ui) ->
        $('#travelRequest_user').val ui.item.id
        return
    
$('#travelRequest_team_manager_ac').autocomplete
    source: Routing.generate 'OpitNotesTravelBundle_travel_userSearch', role: 'team_manager'
    minLength: 2
    select: (event, ui) ->
        $('#travelRequest_team_manager').val ui.item.id
        return
    
$('#travelRequest_general_manager_ac').autocomplete
    source: Routing.generate 'OpitNotesTravelBundle_travel_userSearch', role: 'general_manager'
    minLength: 2
    select: (event, ui) ->
        $('#travelRequest_general_manager').val ui.item.id
        return

#for adding new destination form
$addDestination = $('<div>')
$addDestination.html '<i class="fa fa-plus-square"></i>Add destination'
$addDestination.addClass 'addFormFieldsetChild formFieldsetButton'
$addDestination.addClass 'form-fieldset-add-button'

$destinationCollection = $('#travelRequest_destinations').append $addDestination
$destinationCollection.data 'index', $destinationCollection.find(':input').length
$addDestination.click (e) ->
    e.preventDefault()
    addForm $destinationCollection, $addDestination
    return
 
#for adding new accomodation form 
$addAccomodation = $('<div>')
$addAccomodation.html '<i class="fa fa-plus-square"></i>Add accomodation'
$addAccomodation.addClass 'addFormFieldsetChild formFieldsetButton'
$addAccomodation.addClass 'form-fieldset-add-button'

$accomodationCollection = $('#travelRequest_accomodations').append $addAccomodation
$accomodationCollection.data 'index', $accomodationCollection.find(':input').length
$addAccomodation.click (e) ->
    e.preventDefault()
    addForm $accomodationCollection, $addAccomodation, true
    return

addForm = ($collectionHolder, $addButton, addListener) ->
    prototype = $collectionHolder.data 'prototype'
    index = $collectionHolder.data 'index'
    newForm = prototype.replace '<label class="required">__name__label__</label>', ''
    newForm = newForm.replace /__name__/g, index

    $newForm = $(newForm)
    $newForm = $newForm.append addFormDeleteButton
    $newForm.addClass 'formFieldsetChild'
    $newForm.addClass 'padding-10 margin-left-1-em margin-bottom-1-em display-inline-block vertical-align-top'

    # add change event listener to number of nights on newly created accomodation
    if addListener
        numberOfNightsListener($newForm)

    $currency = $newForm.find('.currency')
    $cost = $newForm.find('.cost')

    $newForm.find('.currency option[value=EUR]').attr('selected','selected')
    
    $cost.parent().append $currency
    
    $collectionHolder.data 'index', index+1
    $addButton.before $newForm

$('#travelRequest_departure_date').on 'change', ->
    compareDays()
$('#travelRequest_arrival_date').on 'change', ->
    compareDays()

# method to validate form before preview
$form = $('#travelRequestForm')

$errorMessages = $('#travelRequestForm ul')
$errorMessages.remove()

# method to validate if arrival date is earlier than departure date
$.validator.addMethod 'compare', (value, element) ->
    departureDate = $('#travelRequest_departure_date').val()
    arrivalDate = $('#travelRequest_arrival_date').val()
    return departureDate <= arrivalDate
, 'Arrival date should not be earlier than departure date.'

# check if element and the hidden id field for the element is empty
$.validator.addMethod 'checkId', (value, element) ->
    $element = $(element)
    id = $(element).attr 'id'
    $idElement = $('#'+id.substr(0, id.length-3))
    
    if $element.val()
        if not $idElement.val() then return false else return true
    else return false

, 'This field is required.'

# assing custom validation rules to arrival date, user, general manager
$form.validate
    ignore: []
    rules:
        "travelRequest[arrival_date]": "compare"
        "travelRequest[user_ac]": "checkId"
        "travelRequest[general_manager_ac]": "checkId"

$( '#travelRequest_add_travel_request' ).click (event) ->
    event.preventDefault()
    
#    validate form on client side
    if $form.valid() and compareDays()
        # if form is valid post ajax request to get the preview
        $.ajax
            method: 'POST'
            url: Routing.generate 'OpitNotesTravelBundle_travel_show_details'
            data: 'preview=1&' + $form.serialize()
        .done (data) ->
            $preview = $('<div id="dialog-travelrequest-preview"></div>').html data
            $preview.dialog
                open: ->
                    $('.ui-dialog-title').append '<i class="fa fa-list-alt"></i> Details'
                close: ->
                    $preview.dialog "destroy"
                width: 550
                maxHeight: $(window).outerHeight()-100
                modal: on
                buttons:
                    Save: ->
                        $form.submit()
                        $preview.dialog "destroy"
                        return
                    'Save & send for approval': ->
                        formAction = $form.attr('action') + '/fa'
                        if isNaN(window.location.href.slice(-1))
                            formAction = $form.attr('action') + '/new/fa'
                        $form.attr 'action', formAction
                        $form.submit()
                        $preview.dialog "destroy"
                        return
                    Cancel: ->
                        $preview.dialog "destroy"
                        return
        .fail () ->
            $('<div></div>').html('The travel request could not be saved due to an error.').dialog
                title: 'Error'
    else
        $('.hasDatepicker').each ->
            if $(@).hasClass 'error'
                $(@).parent().find('.fa-calendar').addClass 'margin-top-12'
    return