$.fn.bgPainter = (options = {}, container) ->
    defaultOptions =
        bgColors: [200, 200, 200]
        substractor1: 50
        substractor2: 20
        
    # merge passed options
    $.extend true, defaultOptions, options
    $container = if container then $(container) else $(@)
    
    styles = ''
    colorIndex = 0
    bgColors = defaultOptions.bgColors.slice 0
    # loop through all elements with class team-employee
    $container.each ->
        elementClass = $(@).data 'class'
        # if current working color leq 0
        if bgColors[colorIndex] <= 0
            # if colors are still left in array
            if colorIndex < (bgColors.length - 1)
                colorIndex++
            else
                colorIndex = 0
            bgColors = defaultOptions.bgColors.slice 0
            
        # loop through all colors
        x = 0
        while x < bgColors.length
            if x is colorIndex
                bgColors[colorIndex] = bgColors[colorIndex] - defaultOptions.substractor1
            else
                bgColors[x] = bgColors[x] - defaultOptions.substractor2
            x++
            
        styles += ".#{ elementClass } { background: rgb(#{ bgColors[0] }, #{ bgColors[1] }, #{ bgColors[2] }) !important; }"
        
    $( "<style>#{ styles }</style>" ).appendTo 'head'
    return

# Register visual export button event listeners
$('#leave-calendar-container #export-button').hover(
    -> $(@).addClass('fc-state-hover'),
    -> $(@).removeClass('fc-state-hover')
)

# Register calendar export event listener
$('#leave-calendar-container #export-button').on 'click.export', ->
    # Fetch current calendar view object
    $calendarView = $("#team-leaves-calendar").fullCalendar 'getView'
    # We want to send data as attachment, traditional form submission is used
    url = Routing.generate 'OpitOpitHrmLeaveBundle_calendar_team_leaves_export'
    data =
        start: $.datepicker.formatDate($.datepicker.ISO_8601, new Date($calendarView.visStart)),
        end: $.datepicker.formatDate($.datepicker.ISO_8601, new Date($calendarView.visEnd)),
        title: $calendarView.title

    # Call form submission helper
    $.fn.download(url, data)

    return

$(document).ready ->
    $("#team-leaves-calendar").fullCalendar
        editable: false
        selectable: false
        firstDay: 1
        events: Routing.generate('OpitOpitHrmLeaveBundle_calendar_team_employees')
        eventAfterRender: (event, element, view) ->
            if event.className.length > 1
                date = event.className[2].split('_')[1]
                $('#leave-calendar-container').find("[data-date='" + date + "']").addClass 'background-color-default-red'

    $('.team-employee').bgPainter()