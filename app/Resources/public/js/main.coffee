$(document).data 'notes', {}
$.extend true, $(document).data('notes'),
    funcs:
        deleteSingleRequest: (type, self) ->
            $checkbox = self.closest('tr').find(':checkbox')
            $checkbox.prop 'checked', true
            # TODO: Add travel request ID to the dialog body text.
            #$('<div></div>').html("Are you sure you want to delete the travel request \"#{travel-request-id}\"?").dialog
            $('<div></div>').html("Are you sure you want to delete the travel #{ type }?").dialog
                title: 'Travel request removal'
                buttons:
                    Yes: ->
                        $.ajax
                          method: 'POST'
                          url: if type is 'expense' then Routing.generate 'OpitNotesTravelBundle_expense_delete' else Routing.generate 'OpitNotesTravelBundle_travel_delete'
                          data: 'id': self.data 'id'
                        .done (data) ->
                            if data is '0' then self.parent().parent().remove()
                            return
                        .fail () ->
                            $('<div></div>').html("The travel #{ type } could not be deleted due to an error.").dialog
                                title: 'Error'
                        $(document).data('notes').funcs.initListPageListeners()
                        $(document).data('notes').funcs.initPager()
                        $(@).dialog 'close'
                        return
                    No: ->
                        # Unset checkbox
                        $checkbox.prop 'checked', false
                        $(@).dialog 'close'
                        return
                close: ->
                    $(@).dialog 'destroy'
                    return
            return
            
        deleteAction: (title, message, url, identifier) ->
          if $(identifier+':checked').length > 0
            $('<div></div>').html('Are you sure you want to delete the '+message+'?').dialog
              title: title
              buttons:
                  Yes: ->
                      $.ajax
                        method: 'POST'
                        url: url
                        data: $(identifier).serialize()
                      .done (data) ->
                        if data[0].userRelated
                            $(document).data('notes').funcs.showAlert data, 'create', 'Deletion not allowed for roles with relations', true
                        else
                          $(identifier+':checked').closest('tr').remove()
                          return
                      .fail () ->
                          $('<div></div>').html('The '+message+' could not be deleted due to an error.').dialog
                              title: 'Error'
                      $(document).data('notes').funcs.initListPageListeners()
                      $(document).data('notes').funcs.initPager()
                      $(@).dialog 'close'
                      return
                  No: ->
                      $(identifier + ':checkbox').attr 'checked', false
                      $(@).dialog 'close'
                      return
              close: ->
                  $(@).dialog 'destroy'
                  return
                
        showAlert: (response, actionType, message, forceClass) ->
            $('#reply-message').addClass "alert-message"
            
            if typeof response is not "string"
                response = $.parseJSON response
                
            if response[0]? and response[0].response == 'error'
              if "update" == actionType or "create" == actionType
                errorString = "<ul>"
                for i in response[0].errorMessage
                  errorString += "<li>"+i+"</li>"
                errorString += "</ul>"
                $('#reply-message')
                  .html("<i class='fa fa-exclamation-triangle'></i> <strong>Error messages:</strong>"+errorString)
                  .removeClass('success-message')
                  .addClass('error-message')
              else if "delete" == actionType
                $('#list-reply-message')
                  .html("<i class='fa fa-exclamation-triangle'></i> Error, while tried to delete the user(s)! <i class='float-right fa fa-chevron-circle-up'></i> ")
                  .removeClass('success-message')
                  .addClass('error-message')
                  .fadeIn(200)
                  .delay(5000)
                  .slideUp(1000)
              returnVal = off
            else
                $('#list-reply-message')
                  .html("<i class='fa fa-check-square'></i> "+message+"! <i class='float-right fa fa-chevron-circle-up'></i> ")
                  .addClass("alert-message")
                  .addClass('success-message')
                  .fadeIn(200)
                  .delay(2000)
                  .slideUp(1000)
                $('#reply-message')
                  .removeClass('alert-message error-message')
                  .empty()
                returnVal = on
              
              if forceClass
                $('#list-reply-message').removeClass('success-message').addClass('error-message')

            return returnVal
            
        createButton: (text, classes, id, $parent = '', redirectAction = '') ->
            $button = $('<div>').html(text)
                        .addClass(classes)
                        .attr('id', id)
               
            if '' != redirectAction
                $button.on 'click', ->
                    window.location.href = Routing.generate redirectAction
               
            if '' != parent
                $parent.append $button
                
            return $button
            
        makeElementToggleAble: (parent, $toggleItems, elementToToggle = '') ->
            $toggleItems.each ->
                $parent = $(@).find(parent)
                self = $(@)
                $toggleIcon = $('<i>')
                                .addClass('fa fa-chevron-up toggle-icon')
                                .addClass 'color-white background-color-orange border-radius-5 cursor-pointer float-right'
                $toggleIcon.on 'click', ->
                    if '' != elementToToggle
                        $elementToToggle = self.find elementToToggle
                        if not $elementToToggle.is(':animated')
                            $toggleIcon.toggleClass 'fa-chevron-down'
                            $elementToToggle.slideToggle()
                    else
                        if not $parent.next().is(':animated')
                            $toggleIcon.toggleClass 'fa-chevron-down'
                            $parent.next().slideToggle()
                $parent.append $toggleIcon
                    
###
 * jQuery datepicker extension
 * Datepicker extended by custom rendering possibility
 *
 * @author Sven Henneböle <henneboele@opit.hu>
 * @version 1.0
 * @depends jQuery
 *
 * @param object  options List of options
###
__picker = $.fn.datepicker

$.fn.datepicker = (options) ->
    __picker.apply this, [options]
    $self = @

    options = options or {}
    defaultOptions =
        wrapper: '<div></div>'
        indicatorIcon: $('<i>')
    # Merge passed options
    $.extend true, defaultOptions, options
    
    if options.showOn isnt 'button'
        $self.attr
            readonly: 'readonly'
        .addClass 'icon-prefix-indent'
        defaultOptions.indicatorIcon.addClass 'fa fa-calendar position-absolute input-prefix-position cursor-pointer'
        defaultOptions.indicatorIcon.click ->
            $(@).parent().parent().children('input').focus()
        $self.before defaultOptions.wrapper
        $self.prev().append defaultOptions.indicatorIcon
    return $self


if not Modernizr.inputtypes.date
    $('input[type=date]').each ->
        name = $(@).attr 'name'
        id = $(@).attr('id')
        $(@).after '<input type="hidden" name="'+name+'" id="altDate'+id+'" />'
        $(@).datepicker {altField:'#altDate'+id, altFormat: 'yy-mm-dd'}
        
$(document).ajaxComplete (event, XMLHttpRequest, ajaxOptions) ->
    id = XMLHttpRequest.responseText.match(/id="([\w|-]+)"/)
    $("##{id[1]} *[title]").tipsy() if id?[1]?
    
$(document).ajaxError (event, request, settings) ->
    if window.location.href.indexOf('login') < -1
        if settings.url.indexOf('unread') > -1
            $sessionTimeout = $('<div id="dialog-travelrequest-preview"></div>').html 'Your session has timed out please login again.'
            $sessionTimeout.dialog
                open: ->
                    $('.ui-dialog-title').append '<i class="fa fa-exclamation-circle"></i> Session timeout'
                width: 550
                maxHeight: $(window).outerHeight()-100
                modal: on
                buttons:
                    Login: ->
                        window.location.href = Routing.generate 'OpitNotesUserBundle_security_login'
    
$(document).ready ->
    $('[title]').each ->
        $(@).tipsy()