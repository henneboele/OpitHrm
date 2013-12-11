// Generated by CoffeeScript 1.6.3
(function() {
  var $subMenuClone, cloneSubmenu, subMenuCloneClass;

  $(document).data('OpitNotesUserBundle', {});

  $.extend(true, $(document).data('OpitNotesUserBundle'), {
    funcs: {
      userEdit: function(userId, successCallback) {
        return $.ajax({
          method: 'GET',
          url: Routing.generate('OpitNotesUserBundle_user_show', {
            id: userId
          })
        }).done(function(data) {
          $('<div id="dialog-edititem"></div>').html(data).dialog({
            open: function() {
              return $('.ui-dialog-title').append('<i class="fa fa-list-alt"></i> Edit User');
            },
            width: 750,
            modal: true,
            buttons: {
              Save: function() {
                return $.ajax({
                  type: 'POST',
                  url: Routing.generate('OpitNotesUserBundle_user_add', {
                    id: userId
                  }),
                  data: $('#adduser_frm').serialize()
                }).done(function(data) {
                  var response, url;
                  url = Routing.generate('OpitNotesUserBundle_user_list');
                  if (url === window.location.pathname) {
                    response = data;
                    $.ajax({
                      type: 'POST',
                      url: url,
                      data: {
                        "showList": 1
                      }
                    }).done(function(data) {
                      var postActions;
                      $('#list-table').html(data);
                      console.log(successCallback);
                      if (successCallback != null) {
                        postActions = successCallback(response, "update", "User modified successfully");
                      }
                      if (postActions || postActions === void 0) {
                        return $('#dialog-edititem').dialog('destroy');
                      }
                    });
                  } else {
                    $('#dialog-edititem').dialog('destroy');
                  }
                });
              },
              Close: function() {
                $('#dialog-edititem').dialog("destroy");
              }
            }
          });
          return;
        });
      }
    }
  });

  $subMenuClone = {};

  subMenuCloneClass = '.subMenuClone';

  $(document).ready(function() {
    $('#loggedInUser').click(function() {
      var _ref;
      return $(document).data('OpitNotesUserBundle').funcs.userEdit($(this).children('span').data('user-id'), (_ref = $(document).data('OpitNotesUserBundle').funcs) != null ? _ref.showAlert : void 0);
    });
    cloneSubmenu();
    $('.menu .mainMenu').click(function() {
      $('.menu .mainMenu').removeClass('active');
      $(this).addClass("active");
      return cloneSubmenu();
    });
    return $(window).scroll(function() {
      if ($('.active').children('.subMenu').offset().top < $(window).scrollTop()) {
        if ($('body').has(subMenuCloneClass).length) {
          $subMenuClone.css({
            display: 'block'
          });
        }
      }
      if ($('.active').children('.subMenu').offset().top > $(window).scrollTop()) {
        if ($('body').has(subMenuCloneClass).length) {
          return $subMenuClone.css({
            display: 'none'
          });
        }
      }
    });
  });

  cloneSubmenu = function() {
    if ($('body').children(subMenuCloneClass).length) {
      $('body').find(subMenuCloneClass).remove();
    }
    $subMenuClone = $('.active').children('.subMenu').clone();
    $subMenuClone.addClass('subMenuClone');
    return $('body').append($subMenuClone);
  };

  $.fn.extend({
    formIsEmpty: function(element) {
      var $el, exists;
      $el = element ? $(element) : $(this);
      exists = false;
      $el.find(':input').each(function() {
        if ($(this).val()) {
          return exists = true;
        }
      });
      return exists;
    },
    checkAll: function(selector) {
      var $el, checkAll;
      $el = selector ? $(selector) : $(this);
      checkAll = $el.filter(':checked').length === $el.length ? false : true;
      return $el.each(function() {
        return $(this).prop('checked', checkAll);
      });
    }
  });

}).call(this);
