"use strict";function edit(o,a){o.preventDefault(),$.pjax({type:"POST",url:actionRenderEditForm,data:{id:a},container:"#edit-pjax",push:!1,replace:!1,scrollTo:!1}).done(function(){showEditModal()})}function showEditModal(){$("#edit-modal").modal("show")}function changePassword(o,a){o.preventDefault(),$.pjax({type:"POST",url:actionRenderChangePasswordForm,data:{id:a},container:"#change-password-pjax",push:!1,replace:!1,scrollTo:!1}).done(function(){showChangePasswordModal()})}function showChangePasswordModal(){$("#change-password-modal").modal("show")}function showRemoveModal(o,a){o.preventDefault(),$("#delete-admin-button-yes").attr("data-id",a),$("#remove-modal").modal("show")}function remove(){var o=$("#delete-admin-button-yes").attr("data-id");$.post(actionRemove,{id:o})}
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImFkbWluL2luZGV4LmpzIl0sIm5hbWVzIjpbImVkaXQiLCJlIiwiaWQiLCJwcmV2ZW50RGVmYXVsdCIsIiQiLCJwamF4IiwidHlwZSIsInVybCIsImFjdGlvblJlbmRlckVkaXRGb3JtIiwiZGF0YSIsImNvbnRhaW5lciIsInB1c2giLCJyZXBsYWNlIiwic2Nyb2xsVG8iLCJkb25lIiwic2hvd0VkaXRNb2RhbCIsIm1vZGFsIiwiY2hhbmdlUGFzc3dvcmQiLCJhY3Rpb25SZW5kZXJDaGFuZ2VQYXNzd29yZEZvcm0iLCJzaG93Q2hhbmdlUGFzc3dvcmRNb2RhbCIsInNob3dSZW1vdmVNb2RhbCIsImF0dHIiLCJyZW1vdmUiLCJwb3N0IiwiYWN0aW9uUmVtb3ZlIl0sIm1hcHBpbmdzIjoiQUFBQSxZQVVBLFNBQVNBLE1BQUtDLEVBQUdDLEdBQ2JELEVBQUVFLGlCQUNGQyxFQUFFQyxNQUNFQyxLQUFNLE9BQ05DLElBQUtDLHFCQUNMQyxNQUFRUCxHQUFJQSxHQUNaUSxVQUFXLGFBQ1hDLE1BQU0sRUFDTkMsU0FBUyxFQUNUQyxVQUFVLElBQ1hDLEtBQUssV0FDSkMsa0JBT1IsUUFBU0EsaUJBQ0xYLEVBQUUsZUFBZVksTUFBTSxRQVMzQixRQUFTQyxnQkFBZWhCLEVBQUdDLEdBQ3ZCRCxFQUFFRSxpQkFDRkMsRUFBRUMsTUFDRUMsS0FBTSxPQUNOQyxJQUFLVywrQkFDTFQsTUFBUVAsR0FBSUEsR0FDWlEsVUFBVyx3QkFDWEMsTUFBTSxFQUNOQyxTQUFTLEVBQ1RDLFVBQVUsSUFDWEMsS0FBSyxXQUNKSyw0QkFPUixRQUFTQSwyQkFDTGYsRUFBRSwwQkFBMEJZLE1BQU0sUUFTdEMsUUFBU0ksaUJBQWdCbkIsRUFBR0MsR0FDeEJELEVBQUVFLGlCQUNGQyxFQUFFLDRCQUE0QmlCLEtBQUssVUFBV25CLEdBQzlDRSxFQUFFLGlCQUFpQlksTUFBTSxRQU03QixRQUFTTSxVQUNMLEdBQUlwQixHQUFLRSxFQUFFLDRCQUE0QmlCLEtBQUssVUFDNUNqQixHQUFFbUIsS0FBS0MsY0FBZ0J0QixHQUFJQSIsImZpbGUiOiJhZG1pbi9pbmRleC5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIGdsb2JhbCBhY3Rpb25SZW5kZXJFZGl0Rm9ybSwgYWN0aW9uUmVuZGVyQ2hhbmdlUGFzc3dvcmRGb3JtLCBhY3Rpb25SZW1vdmUgKi9cclxuXHJcbi8qKlxyXG4gKiBSZW5kZXJzIGFkbWluaXN0cmF0b3IvbW9kZXJhdG9yIGluZm9ybWF0aW9uIGVkaXQgZm9ybVxyXG4gKlxyXG4gKiBAcGFyYW0ge29iamVjdH0gZSBFdmVudCBvYmplY3RcclxuICogQHBhcmFtIHtudW1lcmljfSBpZCBBZG1pbiBJRFxyXG4gKi9cclxuZnVuY3Rpb24gZWRpdChlLCBpZCkge1xyXG4gICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xyXG4gICAgJC5wamF4KHtcclxuICAgICAgICB0eXBlOiAnUE9TVCcsXHJcbiAgICAgICAgdXJsOiBhY3Rpb25SZW5kZXJFZGl0Rm9ybSxcclxuICAgICAgICBkYXRhOiB7aWQ6IGlkfSxcclxuICAgICAgICBjb250YWluZXI6ICcjZWRpdC1wamF4JyxcclxuICAgICAgICBwdXNoOiBmYWxzZSxcclxuICAgICAgICByZXBsYWNlOiBmYWxzZSxcclxuICAgICAgICBzY3JvbGxUbzogZmFsc2VcclxuICAgIH0pLmRvbmUoZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIHNob3dFZGl0TW9kYWwoKTtcclxuICAgIH0pO1xyXG59XHJcblxyXG4vKipcclxuICogU2hvd3MgYWRtaW5pc3RyYXRvci9tb2RlcmF0b3IgaW5mb3JtYXRpb24gZWRpdCBtb2RhbFxyXG4gKi9cclxuZnVuY3Rpb24gc2hvd0VkaXRNb2RhbCgpIHtcclxuICAgICQoJyNlZGl0LW1vZGFsJykubW9kYWwoJ3Nob3cnKTtcclxufVxyXG5cclxuLyoqXHJcbiAqIFJlbmRlcnMgYWRtaW5pc3RyYXRvci9tb2RlcmF0b3IgcGFzc3dvcmQgY2hhbmdlIGZvcm1cclxuICpcclxuICogQHBhcmFtIHtvYmplY3R9IGUgRXZlbnQgb2JqZWN0XHJcbiAqIEBwYXJhbSB7bnVtZXJpY30gaWQgQWRtaW4gSURcclxuICovXHJcbmZ1bmN0aW9uIGNoYW5nZVBhc3N3b3JkKGUsIGlkKSB7XHJcbiAgICBlLnByZXZlbnREZWZhdWx0KCk7XHJcbiAgICAkLnBqYXgoe1xyXG4gICAgICAgIHR5cGU6ICdQT1NUJyxcclxuICAgICAgICB1cmw6IGFjdGlvblJlbmRlckNoYW5nZVBhc3N3b3JkRm9ybSxcclxuICAgICAgICBkYXRhOiB7aWQ6IGlkfSxcclxuICAgICAgICBjb250YWluZXI6ICcjY2hhbmdlLXBhc3N3b3JkLXBqYXgnLFxyXG4gICAgICAgIHB1c2g6IGZhbHNlLFxyXG4gICAgICAgIHJlcGxhY2U6IGZhbHNlLFxyXG4gICAgICAgIHNjcm9sbFRvOiBmYWxzZVxyXG4gICAgfSkuZG9uZShmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgc2hvd0NoYW5nZVBhc3N3b3JkTW9kYWwoKTtcclxuICAgIH0pO1xyXG59XHJcblxyXG4vKipcclxuICogU2hvd3MgYWRtaW5pc3RyYXRvci9tb2RlcmF0b3IgcGFzc3dvcmQgY2hhbmdlIG1vZGFsXHJcbiAqL1xyXG5mdW5jdGlvbiBzaG93Q2hhbmdlUGFzc3dvcmRNb2RhbCgpIHtcclxuICAgICQoJyNjaGFuZ2UtcGFzc3dvcmQtbW9kYWwnKS5tb2RhbCgnc2hvdycpO1xyXG59XHJcblxyXG4vKipcclxuICogU2hvd3MgYWRtaW5pc3RyYXRvci9tb2RlcmF0b3IgcmVtb3ZlIG1vZGFsXHJcbiAqXHJcbiAqIEBwYXJhbSB7b2JqZWN0fSBlIEV2ZW50IG9iamVjdFxyXG4gKiBAcGFyYW0ge251bWVyaWN9IGlkIEFkbWluIElEXHJcbiAqL1xyXG5mdW5jdGlvbiBzaG93UmVtb3ZlTW9kYWwoZSwgaWQpIHtcclxuICAgIGUucHJldmVudERlZmF1bHQoKTtcclxuICAgICQoJyNkZWxldGUtYWRtaW4tYnV0dG9uLXllcycpLmF0dHIoJ2RhdGEtaWQnLCBpZCk7XHJcbiAgICAkKCcjcmVtb3ZlLW1vZGFsJykubW9kYWwoJ3Nob3cnKTtcclxufVxyXG5cclxuLyoqXHJcbiAqIFJlbW92ZXMgYWRtaW5pc3RyYXRvci9tb2RlcmF0b3JcclxuICovXHJcbmZ1bmN0aW9uIHJlbW92ZSgpIHtcclxuICAgIHZhciBpZCA9ICQoJyNkZWxldGUtYWRtaW4tYnV0dG9uLXllcycpLmF0dHIoJ2RhdGEtaWQnKTtcclxuICAgICQucG9zdChhY3Rpb25SZW1vdmUsIHtpZDogaWR9KTtcclxufSJdfQ==