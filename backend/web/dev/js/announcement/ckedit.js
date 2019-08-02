// CKEDITOR.on('dialogDefinition', function(e) {
//     if (e.data.name === 'link') {
//         var target = e.data.definition.getContents('target');
//         var options = target.get('linkTargetType').items;
//         for (var i = options.length-1; i >= 0; i--) {
//             var label = options[i][0];
//             if (!label.match(/new window/i)) {
//                 options.splice(i, 1);
//             }
//         }
//         var targetField = target.get( 'linkTargetType' );
//         targetField['default'] = '_blank';
//     }
// });
//
// if ($.fn.modal typeof !== 'undefined') {
//     $.fn.modal.Constructor.prototype.enforceFocus = function() {
//         var $modalElement = this.$element;
//         $(document).on('focusin.modal',function(e) {
//             if ($modalElement[0] !== e.target
//                 && !$modalElement.has(e.target).length
//                 && $(e.target).parentsUntil('*[role="dialog"]').length === 0) {
//                 $modalElement.focus();
//             }
//         });
//     };
// }
