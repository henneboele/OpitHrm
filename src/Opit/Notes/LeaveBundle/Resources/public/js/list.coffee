$.extend true, $(document).data('notes'),
    funcs:
        searchCallback: ($form) ->
            data = {}
            $form.serializeArray().map (x) ->
                data[x.name.replace(/\w+\[(\w+)\]/, "$1")] = x.value
                return

            startDate = new Date(data.startDate) if data.startDate
            endDate = new Date(data.endDate) if data.endDate
            highlightClass = 'background-color-green border-radius-2'

            # Highlight all matching leave dates in the result
            $('ul.leave-dates li').each ->
                container = $(@).children 'span'
                if (startDate and endDate)
                    if startDate <= new Date($(@).data('start-date')) and endDate >= new Date($(@).data('end-date'))
                        container.addClass highlightClass
                else
                    if startDate and startDate <= new Date($(@).data('start-date'))
                        container.addClass highlightClass
                        return true
                    if endDate and endDate >= new Date($(@).data('end-date'))
                        container.addClass highlightClass

            return

$(document).ready ->
    $('#main-wrapper').on 'click', '.delete-leave-request', (event) ->
        event.preventDefault()
        $deleteButton = $(@)
        leaveRequestId = $deleteButton.data 'request'
        $('<div id="dialog-show-details-tr"></div>').html("Are you sure you want to delete leave request #{ leaveRequestId }?")
            .dialog
                title: '<i class="fa fa fa-exclamation-triangle"></i> Delete leave request'
                width: 550
                maxHeight: $(window).outerHeight()-100
                modal: on
                buttons:
                    Yes: ->
                        $.ajax
                            method: 'POST'
                            url: $deleteButton.attr('href')
                            data: 'id': $deleteButton.data('id')
                        .done (data) ->
                            $('#dialog-show-details-tr').dialog 'destroy'
                            $deleteButton.closest('tr').remove()
                            return
                    No: ->
                        $('#dialog-show-details-tr').dialog 'destroy'
                        return
                        
    $('#leave_list').on 'change', '.changeState', ->
        $(document).data('notes').funcs.changeStateDialog $(@), $(document).data('notes').funcs.changeLeaveRequestStatus, $(@).data('lr'), 'leave'        

inverse = false
$('form').on 'click', '.fa-sort', ->
    inverse = $(document).data('notes').funcs.clientSideListOrdering $(@), inverse
    
$('form').on 'click', '.order-text', ->
    inverse = $(document).data('notes').funcs.clientSideListOrdering $(@).parent().find('i'), inverse