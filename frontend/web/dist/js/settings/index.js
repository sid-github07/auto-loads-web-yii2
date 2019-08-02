"use strict";function showChangeEmailModal(){$(".change-email").click(function(){$("#change-email").modal("show")})}function showChangeVatCodeModal(){$(".change-vat-code").click(function(){$("#change-vat-code").modal("show")})}function getDocumentType(t){return t.closest(".document-pjax").data("document-type")}function getPjaxContainerId(t){return t.closest(".document-pjax").attr("id")}function addDocument(){$(".document-file").change(function(t){var e=getDocumentType($(this)),n=$(this).attr("name");documents[e]={file:t.target.files,name:n}})}function getUploadAction(t,e){var n=$("#"+e+'[data-document-type="'+t+'"] .document-date').val(),o=$("#"+e+'[data-document-type="'+t+'"] .document-form').attr("action");return n.length>0&&(o+="/"+n),o}function getDocument(t){var e=new FormData;return $.each(documents[t].file,function(n,o){e.append(documents[t].name,o)}),e}function uploadDocument(t,e){$.pjax({type:"POST",url:getUploadAction(t,e),data:getDocument(t),container:"#"+e,push:!1,scrollTo:!1,cache:!1,processData:!1,contentType:!1})}function submitForm(){$(".document-submit").click(function(){uploadDocument(getDocumentType($(this)),getPjaxContainerId($(this)))})}function removeDocument(t,e){$.pjax({type:"POST",url:t,container:"#"+e,push:!1,scrollTo:!1})}function deleteDocument(){$(".document-remove").click(function(t){t.preventDefault(),removeDocument($(this).attr("href"),getPjaxContainerId($(this)))})}function showDocumentForm(){$(".document-update").click(function(){var t=getDocumentType($(this)),e=getPjaxContainerId($(this));$("#"+e+'[data-document-type="'+t+'"] .document-form-container').removeClass("hidden")})}function hideDocumentForm(){$(".document-form-close").click(function(){var t=getDocumentType($(this)),e=getPjaxContainerId($(this));$("#"+e+'[data-document-type="'+t+'"] .document-form-container').addClass("hidden")})}var documents={};$(document).on("ready pjax:end",function(){showChangeEmailModal(),showChangeVatCodeModal(),addDocument(),submitForm(),deleteDocument(),showDocumentForm(),hideDocumentForm()}),$(document).on("pjax:success","#document-cmr, #document-eu, #document-im",function(){$.pjax.reload({container:"#document-toastr",timeout:2e3})});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInNldHRpbmdzL2luZGV4LmpzIl0sIm5hbWVzIjpbInNob3dDaGFuZ2VFbWFpbE1vZGFsIiwiJCIsImNsaWNrIiwibW9kYWwiLCJzaG93Q2hhbmdlVmF0Q29kZU1vZGFsIiwiZ2V0RG9jdW1lbnRUeXBlIiwiZWxlbWVudCIsImNsb3Nlc3QiLCJkYXRhIiwiZ2V0UGpheENvbnRhaW5lcklkIiwiYXR0ciIsImFkZERvY3VtZW50IiwiY2hhbmdlIiwiZXZlbnQiLCJ0eXBlIiwidGhpcyIsIm5hbWUiLCJkb2N1bWVudHMiLCJmaWxlIiwidGFyZ2V0IiwiZmlsZXMiLCJnZXRVcGxvYWRBY3Rpb24iLCJjb250YWluZXIiLCJkYXRlIiwidmFsIiwiYWN0aW9uIiwibGVuZ3RoIiwiZ2V0RG9jdW1lbnQiLCJGb3JtRGF0YSIsImVhY2giLCJrZXkiLCJ2YWx1ZSIsImFwcGVuZCIsInVwbG9hZERvY3VtZW50IiwicGpheCIsInVybCIsInB1c2giLCJzY3JvbGxUbyIsImNhY2hlIiwicHJvY2Vzc0RhdGEiLCJjb250ZW50VHlwZSIsInN1Ym1pdEZvcm0iLCJyZW1vdmVEb2N1bWVudCIsImRlbGV0ZURvY3VtZW50IiwicHJldmVudERlZmF1bHQiLCJzaG93RG9jdW1lbnRGb3JtIiwicmVtb3ZlQ2xhc3MiLCJoaWRlRG9jdW1lbnRGb3JtIiwiYWRkQ2xhc3MiLCJkb2N1bWVudCIsIm9uIiwicmVsb2FkIiwidGltZW91dCJdLCJtYXBwaW5ncyI6IkFBQUEsWUFRQSxTQUFTQSx3QkFDTEMsRUFBRSxpQkFBaUJDLE1BQU0sV0FDckJELEVBQUUsaUJBQWlCRSxNQUFNLFVBT2pDLFFBQVNDLDBCQUNMSCxFQUFFLG9CQUFvQkMsTUFBTSxXQUN4QkQsRUFBRSxvQkFBb0JFLE1BQU0sVUFVcEMsUUFBU0UsaUJBQWdCQyxHQUNyQixNQUFPQSxHQUFRQyxRQUFRLGtCQUFrQkMsS0FBSyxpQkFTbEQsUUFBU0Msb0JBQW1CSCxHQUN4QixNQUFPQSxHQUFRQyxRQUFRLGtCQUFrQkcsS0FBSyxNQU1sRCxRQUFTQyxlQUNMVixFQUFFLGtCQUFrQlcsT0FBTyxTQUFVQyxHQUVqQyxHQUFJQyxHQUFPVCxnQkFBZ0JKLEVBQUVjLE9BRXpCQyxFQUFPZixFQUFFYyxNQUFNTCxLQUFLLE9BQ3hCTyxXQUFVSCxJQUNOSSxLQUFNTCxFQUFNTSxPQUFPQyxNQUNuQkosS0FBTUEsS0FZbEIsUUFBU0ssaUJBQWdCUCxFQUFNUSxHQUUzQixHQUFJQyxHQUFPdEIsRUFBRSxJQUFNcUIsRUFBWSx3QkFBMEJSLEVBQU8scUJBQXFCVSxNQUVqRkMsRUFBU3hCLEVBQUUsSUFBTXFCLEVBQVksd0JBQTBCUixFQUFPLHFCQUFxQkosS0FBSyxTQUk1RixPQUhJYSxHQUFLRyxPQUFTLElBQ2RELEdBQVUsSUFBTUYsR0FFYkUsRUFTWCxRQUFTRSxhQUFZYixHQUVqQixHQUFJTixHQUFPLEdBQUlvQixTQUlmLE9BSEEzQixHQUFFNEIsS0FBS1osVUFBVUgsR0FBTUksS0FBTSxTQUFVWSxFQUFLQyxHQUN4Q3ZCLEVBQUt3QixPQUFPZixVQUFVSCxHQUFNRSxLQUFNZSxLQUUvQnZCLEVBU1gsUUFBU3lCLGdCQUFlbkIsRUFBTVEsR0FDMUJyQixFQUFFaUMsTUFDRXBCLEtBQU0sT0FDTnFCLElBQUtkLGdCQUFnQlAsRUFBTVEsR0FDM0JkLEtBQU1tQixZQUFZYixHQUNsQlEsVUFBVyxJQUFNQSxFQUNqQmMsTUFBTSxFQUNOQyxVQUFVLEVBQ1ZDLE9BQU8sRUFDUEMsYUFBYSxFQUNiQyxhQUFhLElBT3JCLFFBQVNDLGNBQ0x4QyxFQUFFLG9CQUFvQkMsTUFBTSxXQUt4QitCLGVBSFc1QixnQkFBZ0JKLEVBQUVjLE9BRWJOLG1CQUFtQlIsRUFBRWMsVUFXN0MsUUFBUzJCLGdCQUFlUCxFQUFLYixHQUN6QnJCLEVBQUVpQyxNQUNFcEIsS0FBTSxPQUNOcUIsSUFBS0EsRUFDTGIsVUFBVyxJQUFNQSxFQUNqQmMsTUFBTSxFQUNOQyxVQUFVLElBT2xCLFFBQVNNLGtCQUNMMUMsRUFBRSxvQkFBb0JDLE1BQU0sU0FBVVcsR0FDbENBLEVBQU0rQixpQkFLTkYsZUFIVXpDLEVBQUVjLE1BQU1MLEtBQUssUUFFUEQsbUJBQW1CUixFQUFFYyxVQVE3QyxRQUFTOEIsb0JBQ0w1QyxFQUFFLG9CQUFvQkMsTUFBTSxXQUV4QixHQUFJWSxHQUFPVCxnQkFBZ0JKLEVBQUVjLE9BRXpCTyxFQUFZYixtQkFBbUJSLEVBQUVjLE1BQ3JDZCxHQUFFLElBQU1xQixFQUFZLHdCQUEwQlIsRUFBTywrQkFBK0JnQyxZQUFZLFlBT3hHLFFBQVNDLG9CQUNMOUMsRUFBRSx3QkFBd0JDLE1BQU0sV0FFNUIsR0FBSVksR0FBT1QsZ0JBQWdCSixFQUFFYyxPQUV6Qk8sRUFBWWIsbUJBQW1CUixFQUFFYyxNQUNyQ2QsR0FBRSxJQUFNcUIsRUFBWSx3QkFBMEJSLEVBQU8sK0JBQStCa0MsU0FBUyxZQTlLckcsR0FBSS9CLGFBcUxKaEIsR0FBRWdELFVBQVVDLEdBQUcsaUJBQWtCLFdBRzdCbEQsdUJBR0FJLHlCQUdBTyxjQUdBOEIsYUFHQUUsaUJBR0FFLG1CQUdBRSxxQkFNSjlDLEVBQUVnRCxVQUFVQyxHQUFHLGVBQWdCLDRDQUE2QyxXQUN4RWpELEVBQUVpQyxLQUFLaUIsUUFDSDdCLFVBQVcsbUJBQ1g4QixRQUFTIiwiZmlsZSI6InNldHRpbmdzL2luZGV4LmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyoqIEBtZW1iZXIge29iamVjdH0gQ29udGFpbmVyIGZvciBkb2N1bWVudHMgKi9cclxudmFyIGRvY3VtZW50cyA9IHt9O1xyXG5cclxuLyoqXHJcbiAqIFNob3dzIGNoYW5nZSBlbWFpbCBtb2RhbCBvbiBjaGFuZ2UgZW1haWwgYnV0dG9uIGNsaWNrXHJcbiAqL1xyXG5mdW5jdGlvbiBzaG93Q2hhbmdlRW1haWxNb2RhbCgpIHtcclxuICAgICQoJy5jaGFuZ2UtZW1haWwnKS5jbGljayhmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgJCgnI2NoYW5nZS1lbWFpbCcpLm1vZGFsKCdzaG93Jyk7XHJcbiAgICB9KTtcclxufVxyXG5cclxuLyoqXHJcbiAqIFNob3dzIGNoYW5nZSBWQVQgY29kZSBtb2RhbCBvbiBjaGFuZ2UgVkFUIGNvZGUgYnV0dG9uIGNsaWNrXHJcbiAqL1xyXG5mdW5jdGlvbiBzaG93Q2hhbmdlVmF0Q29kZU1vZGFsKCkge1xyXG4gICAgJCgnLmNoYW5nZS12YXQtY29kZScpLmNsaWNrKGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAkKCcjY2hhbmdlLXZhdC1jb2RlJykubW9kYWwoJ3Nob3cnKTtcclxuICAgIH0pO1xyXG59XHJcblxyXG4vKipcclxuICogUmV0dXJucyBkb2N1bWVudCB0eXBlXHJcbiAqXHJcbiAqIEBwYXJhbSB7b2JqZWN0fSBlbGVtZW50IEN1cnJlbnQgZG9jdW1lbnQgZWxlbWVudFxyXG4gKiBAcmV0dXJucyB7Kn1cclxuICovXHJcbmZ1bmN0aW9uIGdldERvY3VtZW50VHlwZShlbGVtZW50KSB7XHJcbiAgICByZXR1cm4gZWxlbWVudC5jbG9zZXN0KCcuZG9jdW1lbnQtcGpheCcpLmRhdGEoJ2RvY3VtZW50LXR5cGUnKTtcclxufVxyXG5cclxuLyoqXHJcbiAqIFJldHVybnMgZG9jdW1lbnQgUEpBWCBjb250YWluZXIgSURcclxuICpcclxuICogQHBhcmFtIHtvYmplY3R9IGVsZW1lbnQgQ3VycmVudCBkb2N1bWVudCBlbGVtZW50XHJcbiAqIEByZXR1cm5zIHsqfVxyXG4gKi9cclxuZnVuY3Rpb24gZ2V0UGpheENvbnRhaW5lcklkKGVsZW1lbnQpIHtcclxuICAgIHJldHVybiBlbGVtZW50LmNsb3Nlc3QoJy5kb2N1bWVudC1wamF4JykuYXR0cignaWQnKTtcclxufVxyXG5cclxuLyoqXHJcbiAqIEFkZHMgc2VsZWN0ZWQgZG9jdW1lbnQgdG8gZG9jdW1lbnQgY29udGFpbmVyXHJcbiAqL1xyXG5mdW5jdGlvbiBhZGREb2N1bWVudCgpIHtcclxuICAgICQoJy5kb2N1bWVudC1maWxlJykuY2hhbmdlKGZ1bmN0aW9uIChldmVudCkge1xyXG4gICAgICAgIC8qKiBAbWVtYmVyIHtzdHJpbmd9IERvY3VtZW50IHR5cGUgKi9cclxuICAgICAgICB2YXIgdHlwZSA9IGdldERvY3VtZW50VHlwZSgkKHRoaXMpKTtcclxuICAgICAgICAvKiogQG1lbWJlciB7c3RyaW5nfSBEb2N1bWVudCB1cGxvYWQgaW5wdXQgbmFtZSAqL1xyXG4gICAgICAgIHZhciBuYW1lID0gJCh0aGlzKS5hdHRyKCduYW1lJyk7XHJcbiAgICAgICAgZG9jdW1lbnRzW3R5cGVdID0ge1xyXG4gICAgICAgICAgICBmaWxlOiBldmVudC50YXJnZXQuZmlsZXMsXHJcbiAgICAgICAgICAgIG5hbWU6IG5hbWVcclxuICAgICAgICB9O1xyXG4gICAgfSk7XHJcbn1cclxuXHJcbi8qKlxyXG4gKiBSZXR1cm5zIHVwbG9hZCBkb2N1bWVudCBmb3JtIGFjdGlvblxyXG4gKlxyXG4gKiBAcGFyYW0ge3N0cmluZ30gdHlwZSBEb2N1bWVudCB0eXBlXHJcbiAqIEBwYXJhbSB7c3RyaW5nfSBjb250YWluZXIgUEpBWCBjb250YWluZXIgSURcclxuICogQHJldHVybnMgeyp8alF1ZXJ5fVxyXG4gKi9cclxuZnVuY3Rpb24gZ2V0VXBsb2FkQWN0aW9uKHR5cGUsIGNvbnRhaW5lcikge1xyXG4gICAgLyoqIEBtZW1iZXIge3N0cmluZ30gRG9jdW1lbnQgZGF0ZSBvZiBleHBpcnkgKi9cclxuICAgIHZhciBkYXRlID0gJCgnIycgKyBjb250YWluZXIgKyAnW2RhdGEtZG9jdW1lbnQtdHlwZT1cIicgKyB0eXBlICsgJ1wiXSAuZG9jdW1lbnQtZGF0ZScpLnZhbCgpO1xyXG4gICAgLyoqIEBtZW1iZXIge3N0cmluZ30gRG9jdW1lbnQgdXBsb2FkIGZvcm0gYWN0aW9uICovXHJcbiAgICB2YXIgYWN0aW9uID0gJCgnIycgKyBjb250YWluZXIgKyAnW2RhdGEtZG9jdW1lbnQtdHlwZT1cIicgKyB0eXBlICsgJ1wiXSAuZG9jdW1lbnQtZm9ybScpLmF0dHIoJ2FjdGlvbicpO1xyXG4gICAgaWYgKGRhdGUubGVuZ3RoID4gMCkge1xyXG4gICAgICAgIGFjdGlvbiArPSAnLycgKyBkYXRlO1xyXG4gICAgfVxyXG4gICAgcmV0dXJuIGFjdGlvbjtcclxufVxyXG5cclxuLyoqXHJcbiAqIFJldHVybnMgZG9jdW1lbnQgZGF0YSwgcmVhZHkgZm9yIFBPU1RcclxuICpcclxuICogQHBhcmFtIHtzdHJpbmd9IHR5cGUgRG9jdW1lbnQgdHlwZVxyXG4gKiBAcmV0dXJucyB7Kn1cclxuICovXHJcbmZ1bmN0aW9uIGdldERvY3VtZW50KHR5cGUpIHtcclxuICAgIC8qKiBAbWVtYmVyIHtvYmplY3R9IERvY3VtZW50IGRhdGEgY29udGFpbmVyICovXHJcbiAgICB2YXIgZGF0YSA9IG5ldyBGb3JtRGF0YSgpO1xyXG4gICAgJC5lYWNoKGRvY3VtZW50c1t0eXBlXS5maWxlLCBmdW5jdGlvbiAoa2V5LCB2YWx1ZSkge1xyXG4gICAgICAgIGRhdGEuYXBwZW5kKGRvY3VtZW50c1t0eXBlXS5uYW1lLCB2YWx1ZSk7XHJcbiAgICB9KTtcclxuICAgIHJldHVybiBkYXRhO1xyXG59XHJcblxyXG4vKipcclxuICogVXBsb2FkcyBkb2N1bWVudFxyXG4gKlxyXG4gKiBAcGFyYW0ge3N0cmluZ30gdHlwZSBEb2N1bWVudCB0eXBlXHJcbiAqIEBwYXJhbSB7c3RyaW5nfSBjb250YWluZXIgUEpBWCBjb250YWluZXIgSURcclxuICovXHJcbmZ1bmN0aW9uIHVwbG9hZERvY3VtZW50KHR5cGUsIGNvbnRhaW5lcikge1xyXG4gICAgJC5wamF4KHtcclxuICAgICAgICB0eXBlOiAnUE9TVCcsXHJcbiAgICAgICAgdXJsOiBnZXRVcGxvYWRBY3Rpb24odHlwZSwgY29udGFpbmVyKSxcclxuICAgICAgICBkYXRhOiBnZXREb2N1bWVudCh0eXBlKSxcclxuICAgICAgICBjb250YWluZXI6ICcjJyArIGNvbnRhaW5lcixcclxuICAgICAgICBwdXNoOiBmYWxzZSxcclxuICAgICAgICBzY3JvbGxUbzogZmFsc2UsXHJcbiAgICAgICAgY2FjaGU6IGZhbHNlLFxyXG4gICAgICAgIHByb2Nlc3NEYXRhOiBmYWxzZSwgLy8gRG9uJ3QgcHJvY2VzcyB0aGUgZmlsZXNcclxuICAgICAgICBjb250ZW50VHlwZTogZmFsc2UgLy8galF1ZXJ5IHdpbGwgdGVsbCB0aGUgc2VydmVyIGl0cyBhIHF1ZXJ5IHN0cmluZyByZXF1ZXN0XHJcbiAgICB9KTtcclxufVxyXG5cclxuLyoqXHJcbiAqIEV4ZWN1dGVzIGFub255bW91cyBmdW5jdGlvbiBvbiBkb2N1bWVudCBzdWJtaXQgYnV0dG9uIGNsaWNrXHJcbiAqL1xyXG5mdW5jdGlvbiBzdWJtaXRGb3JtKCkge1xyXG4gICAgJCgnLmRvY3VtZW50LXN1Ym1pdCcpLmNsaWNrKGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAvKiogQG1lbWJlciB7c3RyaW5nfSBEb2N1bWVudCB0eXBlICovXHJcbiAgICAgICAgdmFyIHR5cGUgPSBnZXREb2N1bWVudFR5cGUoJCh0aGlzKSk7XHJcbiAgICAgICAgLyoqIEBtZW1iZXIge3N0cmluZ30gUEpBWCBjb250YWluZXIgSUQgKi9cclxuICAgICAgICB2YXIgY29udGFpbmVyID0gZ2V0UGpheENvbnRhaW5lcklkKCQodGhpcykpO1xyXG4gICAgICAgIHVwbG9hZERvY3VtZW50KHR5cGUsIGNvbnRhaW5lcik7XHJcbiAgICB9KTtcclxufVxyXG5cclxuLyoqXHJcbiAqIFJlbW92ZXMgZG9jdW1lbnRcclxuICpcclxuICogQHBhcmFtIHtzdHJpbmd9IHVybCBVUkwgdG8gZG9jdW1lbnQgcmVtb3ZlIGFjdGlvblxyXG4gKiBAcGFyYW0ge3N0cmluZ30gY29udGFpbmVyIFBKQVggY29udGFpbmVyIElEXHJcbiAqL1xyXG5mdW5jdGlvbiByZW1vdmVEb2N1bWVudCh1cmwsIGNvbnRhaW5lcikge1xyXG4gICAgJC5wamF4KHtcclxuICAgICAgICB0eXBlOiAnUE9TVCcsXHJcbiAgICAgICAgdXJsOiB1cmwsXHJcbiAgICAgICAgY29udGFpbmVyOiAnIycgKyBjb250YWluZXIsXHJcbiAgICAgICAgcHVzaDogZmFsc2UsXHJcbiAgICAgICAgc2Nyb2xsVG86IGZhbHNlXHJcbiAgICB9KTtcclxufVxyXG5cclxuLyoqXHJcbiAqIEV4ZWN1dGVzIGFub255bW91cyBmdW5jdGlvbiBvbiBkb2N1bWVudCByZW1vdmUgYnV0dG9uIGNsaWNrXHJcbiAqL1xyXG5mdW5jdGlvbiBkZWxldGVEb2N1bWVudCgpIHtcclxuICAgICQoJy5kb2N1bWVudC1yZW1vdmUnKS5jbGljayhmdW5jdGlvbiAoZXZlbnQpIHtcclxuICAgICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xyXG4gICAgICAgIC8qKiBAbWVtYmVyIHtzdHJpbmd9IFVSTCB0byBkb2N1bWVudCByZW1vdmUgYWN0aW9uICovXHJcbiAgICAgICAgdmFyIHVybCA9ICQodGhpcykuYXR0cignaHJlZicpO1xyXG4gICAgICAgIC8qKiBAbWVtYmVyIHtzdHJpbmd9IFBKQVggY29udGFpbmVyIElEICovXHJcbiAgICAgICAgdmFyIGNvbnRhaW5lciA9IGdldFBqYXhDb250YWluZXJJZCgkKHRoaXMpKTtcclxuICAgICAgICByZW1vdmVEb2N1bWVudCh1cmwsIGNvbnRhaW5lcik7XHJcbiAgICB9KTtcclxufVxyXG5cclxuLyoqXHJcbiAqIEV4ZWN1dGVzIGFub255bW91cyBmdW5jdGlvbiBvbiBkb2N1bWVudCB1cGRhdGUgYnV0dG9uIGNsaWNrXHJcbiAqL1xyXG5mdW5jdGlvbiBzaG93RG9jdW1lbnRGb3JtKCkge1xyXG4gICAgJCgnLmRvY3VtZW50LXVwZGF0ZScpLmNsaWNrKGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAvKiogQG1lbWJlciB7c3RyaW5nfSBEb2N1bWVudCB0eXBlICovXHJcbiAgICAgICAgdmFyIHR5cGUgPSBnZXREb2N1bWVudFR5cGUoJCh0aGlzKSk7XHJcbiAgICAgICAgLyoqIEBtZW1iZXIge3N0cmluZ30gUEpBWCBjb250YWluZXIgSUQgKi9cclxuICAgICAgICB2YXIgY29udGFpbmVyID0gZ2V0UGpheENvbnRhaW5lcklkKCQodGhpcykpO1xyXG4gICAgICAgICQoJyMnICsgY29udGFpbmVyICsgJ1tkYXRhLWRvY3VtZW50LXR5cGU9XCInICsgdHlwZSArICdcIl0gLmRvY3VtZW50LWZvcm0tY29udGFpbmVyJykucmVtb3ZlQ2xhc3MoJ2hpZGRlbicpO1xyXG4gICAgfSk7XHJcbn1cclxuXHJcbi8qKlxyXG4gKiBFeGVjdXRlcyBhbm9ueW1vdXMgZnVuY3Rpb24gb24gZG9jdW1lbnQgZm9ybSBjbG9jayBidXR0b24gY2xpY2tcclxuICovXHJcbmZ1bmN0aW9uIGhpZGVEb2N1bWVudEZvcm0oKSB7XHJcbiAgICAkKCcuZG9jdW1lbnQtZm9ybS1jbG9zZScpLmNsaWNrKGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAvKiogQG1lbWJlciB7c3RyaW5nfSBEb2N1bWVudCB0eXBlICovXHJcbiAgICAgICAgdmFyIHR5cGUgPSBnZXREb2N1bWVudFR5cGUoJCh0aGlzKSk7XHJcbiAgICAgICAgLyoqIEBtZW1iZXIge3N0cmluZ30gUEpBWCBjb250YWluZXIgSUQgKi9cclxuICAgICAgICB2YXIgY29udGFpbmVyID0gZ2V0UGpheENvbnRhaW5lcklkKCQodGhpcykpO1xyXG4gICAgICAgICQoJyMnICsgY29udGFpbmVyICsgJ1tkYXRhLWRvY3VtZW50LXR5cGU9XCInICsgdHlwZSArICdcIl0gLmRvY3VtZW50LWZvcm0tY29udGFpbmVyJykuYWRkQ2xhc3MoJ2hpZGRlbicpO1xyXG4gICAgfSk7XHJcbn1cclxuXHJcbi8qKlxyXG4gKiBFeGVjdXRlcyBhbm9ueW1vdXMgZnVuY3Rpb24gd2hlbiBkb2N1bWVudHMgZnVsbHkgbG9hZHNcclxuICovXHJcbiQoZG9jdW1lbnQpLm9uKCdyZWFkeSBwamF4OmVuZCcsIGZ1bmN0aW9uICgpIHtcclxuICAgIFxyXG4gICAgLyoqIFNob3dzIGNoYW5nZSBlbWFpbCBtb2RhbCBvbiBjaGFuZ2UgZW1haWwgYnV0dG9uIGNsaWNrICovXHJcbiAgICBzaG93Q2hhbmdlRW1haWxNb2RhbCgpO1xyXG5cclxuICAgIC8qKiBTaG93cyBjaGFuZ2UgVkFUIGNvZGUgbW9kYWwgb24gY2hhbmdlIFZBVCBjb2RlIGJ1dHRvbiBjbGljayAqL1xyXG4gICAgc2hvd0NoYW5nZVZhdENvZGVNb2RhbCgpO1xyXG5cclxuICAgIC8qKiBBZGRzIHNlbGVjdGVkIGRvY3VtZW50IHRvIGRvY3VtZW50IGNvbnRhaW5lciAqL1xyXG4gICAgYWRkRG9jdW1lbnQoKTtcclxuXHJcbiAgICAvKiogRXhlY3V0ZXMgYW5vbnltb3VzIGZ1bmN0aW9uIG9uIGRvY3VtZW50IHN1Ym1pdCBidXR0b24gY2xpY2sgKi9cclxuICAgIHN1Ym1pdEZvcm0oKTtcclxuXHJcbiAgICAvKiogRXhlY3V0ZXMgYW5vbnltb3VzIGZ1bmN0aW9uIG9uIGRvY3VtZW50IHJlbW92ZSBidXR0b24gY2xpY2sgKi9cclxuICAgIGRlbGV0ZURvY3VtZW50KCk7XHJcblxyXG4gICAgLyoqIEV4ZWN1dGVzIGFub255bW91cyBmdW5jdGlvbiBvbiBkb2N1bWVudCB1cGRhdGUgYnV0dG9uIGNsaWNrICovXHJcbiAgICBzaG93RG9jdW1lbnRGb3JtKCk7XHJcblxyXG4gICAgLyoqIEV4ZWN1dGVzIGFub255bW91cyBmdW5jdGlvbiBvbiBkb2N1bWVudCBmb3JtIGNsb2NrIGJ1dHRvbiBjbGljayAqL1xyXG4gICAgaGlkZURvY3VtZW50Rm9ybSgpO1xyXG59KTtcclxuXHJcbi8qKlxyXG4gKiBFeGVjdXRlcyBhbm9ueW1vdXMgZnVuY3Rpb24gb24gc3VjY2Vzc2Z1bCBzcGVjaWZpYyBQSkFYIGNvbnRhaW5lcnMgdXBkYXRlXHJcbiAqL1xyXG4kKGRvY3VtZW50KS5vbigncGpheDpzdWNjZXNzJywgJyNkb2N1bWVudC1jbXIsICNkb2N1bWVudC1ldSwgI2RvY3VtZW50LWltJywgZnVuY3Rpb24gKCkge1xyXG4gICAgJC5wamF4LnJlbG9hZCh7XHJcbiAgICAgICAgY29udGFpbmVyOiAnI2RvY3VtZW50LXRvYXN0cicsXHJcbiAgICAgICAgdGltZW91dDogMmUzXHJcbiAgICB9KTtcclxufSk7Il19
