(function($) {
  'use strict';
  $('#defaultconfig').maxlength({
    warningClass: "badge mt-1 badge-approved",
    limitReachedClass: "badge mt-1 badge-denied"
  });

  $('#defaultconfig-2').maxlength({
    alwaysShow: true,
    threshold: 20,
    warningClass: "badge mt-1 badge-approved",
    limitReachedClass: "badge mt-1 badge-denied"
  });

  $('#defaultconfig-3').maxlength({
    alwaysShow: true,
    threshold: 10,
    warningClass: "badge mt-1 badge-approved",
    limitReachedClass: "badge mt-1 badge-denied",
    separator: ' of ',
    preText: 'You have ',
    postText: ' chars remaining.',
    validate: true
  });

  $('#maxlength-textarea').maxlength({
    alwaysShow: true,
    warningClass: "badge mt-1 badge-approved",
    limitReachedClass: "badge mt-1 badge-denied"
  });
})(jQuery);