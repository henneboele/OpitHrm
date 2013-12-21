// Generated by CoffeeScript 1.6.3
(function() {
  var __picker;

  $(document).data('notes', {});

  $.extend(true, $(document).data('notes'), {
    funcs: {
      deleteAction: function(title, message, url, identifier) {
        if ($(identifier + ':checked').length > 0) {
          return $('<div></div>').html('Are you sure you want to delete the ' + message + '?').dialog({
            title: title,
            buttons: {
              Yes: function() {
                $.ajax({
                  method: 'POST',
                  url: url,
                  data: $(identifier).serialize()
                }).done(function(data) {
                  if (data[0].userRelated) {
                    return $(document).data('notes').funcs.showAlert(data, 'create', 'Deletion not allowed for roles with relations', true);
                  } else {
                    $(identifier + ':checked').closest('tr').remove();
                  }
                }).fail(function() {
                  return $('<div></div>').html('The ' + message + ' could not be deleted due to an error.').dialog({
                    title: 'Error'
                  });
                });
                $(this).dialog('close');
              },
              No: function() {
                $(identifier + ':checkbox').attr('checked', false);
                $(this).dialog('close');
              }
            },
            close: function() {
              $(this).dialog('destroy');
            }
          });
        }
      },
      showAlert: function(response, actionType, message, forceClass) {
        var errorString, i, returnVal, _i, _len, _ref;
        $('#reply-message').addClass("alert-message");
        if (typeof response === !"string") {
          response = $.parseJSON(response);
        }
        if ((response[0] != null) && response[0].response === 'error') {
          if ("update" === actionType || "create" === actionType) {
            errorString = "<ul>";
            _ref = response[0].errorMessage;
            for (_i = 0, _len = _ref.length; _i < _len; _i++) {
              i = _ref[_i];
              errorString += "<li>" + i + "</li>";
            }
            errorString += "</ul>";
            $('#reply-message').html("<i class='fa fa-exclamation-triangle'></i> <strong>Error messages:</strong>" + errorString).removeClass('success-message').addClass('error-message');
          } else if ("delete" === actionType) {
            $('#list-reply-message').html("<i class='fa fa-exclamation-triangle'></i> Error, while tried to delete the user(s)! <i class='float-right fa fa-chevron-circle-up'></i> ").removeClass('success-message').addClass('error-message').fadeIn(200).delay(5000).slideUp(1000);
          }
          returnVal = false;
        } else {
          $('#list-reply-message').html("<i class='fa fa-check-square'></i> " + message + "! <i class='float-right fa fa-chevron-circle-up'></i> ").addClass("alert-message").addClass('success-message').fadeIn(200).delay(2000).slideUp(1000);
          returnVal = true;
        }
        if (forceClass) {
          $('#list-reply-message').removeClass('success-message').addClass('error-message');
        }
        return returnVal;
      }
    }
  });

  /*
   * jQuery datepicker extension
   * Datepicker extended by custom rendering possibility
   *
   * @author Sven Henneböle <henneboele@opit.hu>
   * @version 1.0
   * @depends jQuery
   *
   * @param object  options List of options
  */


  __picker = $.fn.datepicker;

  $.fn.datepicker = function(options) {
    var $self, defaultOptions;
    __picker.apply(this, [options]);
    $self = this;
    options = options || {};
    defaultOptions = {
      wrapper: '<span class="relative"></span>',
      indicatorIcon: $('<i>')
    };
    $.extend(true, defaultOptions, options);
    if (options.showOn !== 'button') {
      $self.attr({
        type: 'text',
        readonly: 'readonly'
      }).addClass('icon-prefix-indent');
      defaultOptions.indicatorIcon.addClass('fa fa-calendar absolute input-prefix-position pointer');
      defaultOptions.indicatorIcon.click(function() {
        return $(this).parent().parent().children('input').focus();
      });
      $self.before(defaultOptions.wrapper);
      $self.prev().append(defaultOptions.indicatorIcon);
    }
    return $self;
  };

  if (!Modernizr.inputtypes.date) {
    $('input[type=date]').each(function() {
      var id, name;
      name = $(this).attr('name');
      id = $(this).attr('id');
      $(this).after('<input type="hidden" name="' + name + '" id="altDate' + id + '" />');
      return $(this).datepicker({
        altField: '#altDate' + id,
        altFormat: 'yy-mm-dd'
      });
    });
  }

  $(document).ajaxComplete(function(event, XMLHttpRequest, ajaxOptions) {
    var id;
    id = XMLHttpRequest.responseText.match(/id="([\w|-]+)"/);
    if ((id != null ? id[1] : void 0) != null) {
      return $("#" + id[1] + " *[title]").tipsy();
    }
  });

  $(document).ready(function() {
    return $('[title]').each(function() {
      return $(this).tipsy();
    });
  });

}).call(this);
