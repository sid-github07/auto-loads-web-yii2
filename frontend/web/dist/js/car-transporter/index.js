"use strict";function logOpenMapAction(){$.post(actionLogTransporterMapOpen,{})}function changeMapCollapseButtonText(e,n){if(n.preventDefault(),mapOpen)var a='<i class="fa fa-plus-circle btn-icon map-link"></i> <span class="btn-text map-link">'+TEXT_SHOW_MAP+"</span>";else var a='<i class="fa fa-minus-circle margin-right-5 btn-icon"></i><span class="btn-text color-black">'+TEXT_HIDE_MAP+"</span>";$(e).siblings().each(function(e,n){n.remove()}),$(e).parent().append(a),!1===mapRendered&&renderMapAjax(),mapOpen=!mapOpen}function changeFiltersCollapseButtonText(e,n){if(n.preventDefault(),filtersOpen)var a='<i class="fa fa-plus-circle btn-icon margin-right-5"></i><span class="btn-text">'+TEXT_SHOW_FILTERS+"</span>";else var a='<i class="fa fa-minus-circle margin-right-5 btn-icon"></i><span class="btn-text color-black">'+TEXT_HIDE_FILTERS+"</span>";$(e).siblings().each(function(e,n){n.remove()}),$(e).parent().append(a),!1===filtersRendered&&renderFiltersAjax(),filtersOpen=!filtersOpen}function filterByUnloadCity(e){var n=$(e).val(),a=window.location.pathname,t=replaceQueryParam("unloadCityId",n,window.location.search);window.history.pushState(null,"",a+t),location.reload()}function replaceQueryParam(e,n,a){var t=new RegExp("([?;&])"+e+"[^&;]*[;&]?"),r=a.replace(t,"$1").replace(/&$/,"");return""===n?r:(r.length>2?r+"&":"?")+(n?e+"="+n:"")}function changePageSize(e){var n=$(e).val(),a=window.location.pathname,t=replaceQueryParam("pageSize",n,window.location.search);window.history.pushState(null,"",a+t),location.reload()}function previewContactInfo(e,n){e.preventDefault(),isFullScreen&&$('#map_canvas div.gm-style button[title="Toggle fullscreen view"]').trigger("click"),$.pjax({type:"POST",url:actionPreview,data:{id:n,showInfo:!0},container:"#contact-info-preview-pjax",push:!1,replace:!1,scrollTo:!1}).done(function(){showContactInfoPreviewModal()})}function showContactInfoPreviewModal(){$("#contact-info-preview-modal").modal("show")}function collapseCarTransporterPreview(e,n){e.preventDefault();var a=$("#car-transporter-preview-"+n),t=a.find(".content"),r=a.parent();0===t.text().length?$.post(actionPreview,{id:n,showInfo:!1},function(e){t.html(e),r.hasClass("hidden")?r.removeClass("hidden"):r.addClass("hidden")}):r.hasClass("hidden")?r.removeClass("hidden"):r.addClass("hidden")}function refreshCarTransporterPreview(e,n){e.preventDefault();var a=$("#car-transporter-preview-"+n);$.ajax({type:"POST",url:$("#car-transporter-creditcode-form").attr("action"),data:$("#car-transporter-creditcode-form").serialize(),dataType:"html",success:function(e){$.ajax({type:"GET",url:"car-transporter/get-msgs-creditcode-state",dataType:"json",success:function(n){if(""!=n.type){var t={closeButton:!0,debug:!1,newestOnTop:!0,progressBar:!1,positionClass:"toast-top-center",preventDuplicates:!0,showDuration:0,hideDuration:1e3,timeOut:45e3,extendedTimeOut:8e3,onShown:function(){$(".alert-container").append($("#toast-container"))}};switch(n.type){case"error":toastr.error(n.message,"",t);break;case"success":toastr.success(n.message,"",t)}}a.html(e)}})}})}function showCarTransporterLink(e,n){e.preventDefault(),$.pjax({type:"POST",url:actionPreviewLink,data:{id:n},container:"#car-transporter-link-preview-pjax",push:!1,replace:!1,scrollTo:!1}).done(function(){$("#car-transporter-link-preview-modal").modal("show")})}function copyCarTransporterLinkToClipboard(){$("#car-transporter-link-field").select(),document.execCommand("Copy")&&($("#car-transporter-link-success-alert").fadeIn("slow",function(){$(this).removeClass("hidden")}),setTimeout(function(){$("#car-transporter-link-success-alert").fadeOut("slow",function(){$(this).addClass("hidden")})},5e3))}function renderMapAjax(){$.pjax({type:"POST",url:actionRenderMap+window.location.search,container:"#C-T-3",cache:!1,async:!0,push:!1}).done(function(){mapRendered=!0,loadScript()})}function renderFiltersAjax(e){$.pjax({type:"POST",url:actionRenderFilters+window.location.search,container:"#filter",cache:!1,async:!0,push:!1}).done(function(){filtersRendered=!0})}function renderLoadContactMapAjax(e,n){var a=$(e).data("transporter");$.ajax({method:"POST",url:actionRenderContactMap,data:{transporter:a},contentType:"application/x-www-form-urlencoded; charset=UTF-8",success:function(a){n.html(a),$(e).data("rendered",!0),$(e).data("map-open",!0)}})}var isFullScreen=!1,mapRendered=!1,filtersRendered=!1,mapOpen=!1,filtersOpen=!1;$(document).bind("webkitfullscreenchange mozfullscreenchange fullscreenchange",function(){isFullScreen=document.fullScreen||document.mozFullScreen||document.webkitIsFullScreen}),$(document).ready(function(e){e("body").on("click",".map-link",function(e){logOpenMapAction()}),e("body").on("click",".btn-text",function(n){e(this).parent().find("input[type='button']").trigger("click")}),e("body").on("click",".btn-icon",function(n){e(this).parent().find("input[type='button']").trigger("click")}),e("body").on("click",".map-button",function(n){var a=e(this),t=a.find(".btn-text"),r=a.find(".btn-icon"),o=a.parent().find(".map-container"),i=e(this).data("rendered"),c=e(this).data("map-open");!1===i&&(renderLoadContactMapAjax(this,o),t.html(closeMapText),r.removeClass("fa-plus-circle").addClass("fa-minus-circle")),!0===c&&!0===i?(t.html(openMapText),o.fadeOut(),a.data("map-open",!1),r.removeClass("fa-minus-circle").addClass("fa-plus-circle")):!1===c&&!0===i&&(t.html(closeMapText),o.fadeIn(),a.data("map-open",!0),r.removeClass("fa-plus-circle").addClass("fa-minus-circle"))}),!0===needToOpenFilters&&e("#filter-btn").click()});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImNhci10cmFuc3BvcnRlci9pbmRleC5qcyJdLCJuYW1lcyI6WyJsb2dPcGVuTWFwQWN0aW9uIiwiJCIsInBvc3QiLCJhY3Rpb25Mb2dUcmFuc3BvcnRlck1hcE9wZW4iLCJjaGFuZ2VNYXBDb2xsYXBzZUJ1dHRvblRleHQiLCJlbGVtZW50IiwiZXZlbnQiLCJwcmV2ZW50RGVmYXVsdCIsIm1hcE9wZW4iLCJodG1sIiwiVEVYVF9TSE9XX01BUCIsIlRFWFRfSElERV9NQVAiLCJzaWJsaW5ncyIsImVhY2giLCJpbmRleCIsIml0ZW0iLCJyZW1vdmUiLCJwYXJlbnQiLCJhcHBlbmQiLCJtYXBSZW5kZXJlZCIsInJlbmRlck1hcEFqYXgiLCJjaGFuZ2VGaWx0ZXJzQ29sbGFwc2VCdXR0b25UZXh0IiwiZmlsdGVyc09wZW4iLCJURVhUX1NIT1dfRklMVEVSUyIsIlRFWFRfSElERV9GSUxURVJTIiwiZmlsdGVyc1JlbmRlcmVkIiwicmVuZGVyRmlsdGVyc0FqYXgiLCJmaWx0ZXJCeVVubG9hZENpdHkiLCJ1bmxvYWRDaXR5SWQiLCJ2YWwiLCJwYXRoTmFtZSIsIndpbmRvdyIsImxvY2F0aW9uIiwicGF0aG5hbWUiLCJxdWVyeVBhcmFtcyIsInJlcGxhY2VRdWVyeVBhcmFtIiwic2VhcmNoIiwiaGlzdG9yeSIsInB1c2hTdGF0ZSIsInJlbG9hZCIsInBhcmFtIiwidmFsdWUiLCJyZWdleCIsIlJlZ0V4cCIsInF1ZXJ5IiwicmVwbGFjZSIsImxlbmd0aCIsImNoYW5nZVBhZ2VTaXplIiwicGFnZVNpemUiLCJwcmV2aWV3Q29udGFjdEluZm8iLCJlIiwiaWQiLCJpc0Z1bGxTY3JlZW4iLCJ0cmlnZ2VyIiwicGpheCIsInR5cGUiLCJ1cmwiLCJhY3Rpb25QcmV2aWV3IiwiZGF0YSIsInNob3dJbmZvIiwiY29udGFpbmVyIiwicHVzaCIsInNjcm9sbFRvIiwiZG9uZSIsInNob3dDb250YWN0SW5mb1ByZXZpZXdNb2RhbCIsIm1vZGFsIiwiY29sbGFwc2VDYXJUcmFuc3BvcnRlclByZXZpZXciLCJ0ZCIsImRpdiIsImZpbmQiLCJ0ciIsInRleHQiLCJjb250ZW50IiwiaGFzQ2xhc3MiLCJyZW1vdmVDbGFzcyIsImFkZENsYXNzIiwicmVmcmVzaENhclRyYW5zcG9ydGVyUHJldmlldyIsImFqYXgiLCJhdHRyIiwic2VyaWFsaXplIiwiZGF0YVR5cGUiLCJzdWNjZXNzIiwibXNnZGF0YSIsImNmZyIsImNsb3NlQnV0dG9uIiwiZGVidWciLCJuZXdlc3RPblRvcCIsInByb2dyZXNzQmFyIiwicG9zaXRpb25DbGFzcyIsInByZXZlbnREdXBsaWNhdGVzIiwic2hvd0R1cmF0aW9uIiwiaGlkZUR1cmF0aW9uIiwidGltZU91dCIsImV4dGVuZGVkVGltZU91dCIsIm9uU2hvd24iLCJ0b2FzdHIiLCJlcnJvciIsIm1lc3NhZ2UiLCJzaG93Q2FyVHJhbnNwb3J0ZXJMaW5rIiwiYWN0aW9uUHJldmlld0xpbmsiLCJjb3B5Q2FyVHJhbnNwb3J0ZXJMaW5rVG9DbGlwYm9hcmQiLCJzZWxlY3QiLCJkb2N1bWVudCIsImV4ZWNDb21tYW5kIiwiZmFkZUluIiwidGhpcyIsInNldFRpbWVvdXQiLCJmYWRlT3V0IiwiYWN0aW9uUmVuZGVyTWFwIiwiY2FjaGUiLCJhc3luYyIsImxvYWRTY3JpcHQiLCJhY3Rpb25SZW5kZXJGaWx0ZXJzIiwicmVuZGVyTG9hZENvbnRhY3RNYXBBamF4IiwidHJhbnNwb3J0ZXJJRCIsIm1ldGhvZCIsImFjdGlvblJlbmRlckNvbnRhY3RNYXAiLCJ0cmFuc3BvcnRlciIsImNvbnRlbnRUeXBlIiwiYmluZCIsImZ1bGxTY3JlZW4iLCJtb3pGdWxsU2NyZWVuIiwid2Via2l0SXNGdWxsU2NyZWVuIiwicmVhZHkiLCJvbiIsImJ1dHRvbiIsImJ0blRleHQiLCJidG5JY29uIiwiY29udGFjdE1hcFJlbmRlcmVkIiwiY2xvc2VNYXBUZXh0Iiwib3Blbk1hcFRleHQiLCJuZWVkVG9PcGVuRmlsdGVycyIsImNsaWNrIl0sIm1hcHBpbmdzIjoiQUFBQSxZQWdFQSxTQUFTQSxvQkFDTEMsRUFBRUMsS0FBS0MsZ0NBUVgsUUFBU0MsNkJBQTRCQyxFQUFTQyxHQUUxQyxHQURBQSxFQUFNQyxpQkFDRkMsUUFDQSxHQUFJQyxHQUFPLHVGQUF5RkMsY0FBZ0IsY0FFcEgsSUFBSUQsR0FBTyxnR0FBa0dFLGNBQWdCLFNBRzlHVixHQUFFSSxHQUFTTyxXQUNqQkMsS0FBSyxTQUFVQyxFQUFPQyxHQUMvQkEsRUFBS0MsV0FFVGYsRUFBRUksR0FBU1ksU0FBU0MsT0FBT1QsSUFDUCxJQUFoQlUsYUFDQUMsZ0JBRUpaLFNBQVdBLFFBR2YsUUFBU2EsaUNBQWdDaEIsRUFBU0MsR0FFOUMsR0FEQUEsRUFBTUMsaUJBQ0ZlLFlBQ0EsR0FBSWIsR0FBTyxtRkFBcUZjLGtCQUFvQixjQUVwSCxJQUFJZCxHQUFPLGdHQUFrR2Usa0JBQW9CLFNBRWxIdkIsR0FBRUksR0FBU08sV0FDakJDLEtBQUssU0FBVUMsRUFBT0MsR0FDL0JBLEVBQUtDLFdBR1RmLEVBQUVJLEdBQVNZLFNBQVNDLE9BQU9ULElBQ0gsSUFBcEJnQixpQkFDQUMsb0JBRUpKLGFBQWVBLFlBUW5CLFFBQVNLLG9CQUFtQnRCLEdBQ3hCLEdBQUl1QixHQUFlM0IsRUFBRUksR0FBU3dCLE1BQzFCQyxFQUFXQyxPQUFPQyxTQUFTQyxTQUMzQkMsRUFBY0Msa0JBQWtCLGVBQWdCUCxFQUFjRyxPQUFPQyxTQUFTSSxPQUNsRkwsUUFBT00sUUFBUUMsVUFBVSxLQUFNLEdBQUlSLEVBQVdJLEdBQzlDRixTQUFTTyxTQVliLFFBQVNKLG1CQUFrQkssRUFBT0MsRUFBT0wsR0FDckMsR0FBSU0sR0FBUSxHQUFJQyxRQUFPLFVBQVlILEVBQVEsZUFDdkNJLEVBQVFSLEVBQU9TLFFBQVFILEVBQU8sTUFBTUcsUUFBUSxLQUFNLEdBRXRELE9BQWMsS0FBVkosRUFDT0csR0FHSEEsRUFBTUUsT0FBUyxFQUFJRixFQUFRLElBQU0sTUFBUUgsRUFBUUQsRUFBUSxJQUFNQyxFQUFRLElBUW5GLFFBQVNNLGdCQUFlMUMsR0FDcEIsR0FBSTJDLEdBQVcvQyxFQUFFSSxHQUFTd0IsTUFDdEJDLEVBQVdDLE9BQU9DLFNBQVNDLFNBQzNCQyxFQUFjQyxrQkFBa0IsV0FBWWEsRUFBVWpCLE9BQU9DLFNBQVNJLE9BQzFFTCxRQUFPTSxRQUFRQyxVQUFVLEtBQU0sR0FBSVIsRUFBV0ksR0FDOUNGLFNBQVNPLFNBU2IsUUFBU1Usb0JBQW1CQyxFQUFHQyxHQUMzQkQsRUFBRTNDLGlCQUNFNkMsY0FDQW5ELEVBQUUsbUVBQW1Fb0QsUUFBUSxTQUVqRnBELEVBQUVxRCxNQUNFQyxLQUFNLE9BQ05DLElBQUtDLGNBQ0xDLE1BQ0lQLEdBQUlBLEVBQ0pRLFVBQVUsR0FFZEMsVUFBVyw2QkFDWEMsTUFBTSxFQUNOaEIsU0FBUyxFQUNUaUIsVUFBVSxJQUNYQyxLQUFLLFdBQ0pDLGdDQU9SLFFBQVNBLCtCQUNML0QsRUFBRSwrQkFBK0JnRSxNQUFNLFFBUzNDLFFBQVNDLCtCQUE4QmhCLEVBQUdDLEdBQ3RDRCxFQUFFM0MsZ0JBRUYsSUFBSTRELEdBQUtsRSxFQUFFLDRCQUE4QmtELEdBQ3JDaUIsRUFBTUQsRUFBR0UsS0FBSyxZQUNkQyxFQUFLSCxFQUFHbEQsUUFFYyxLQUF0Qm1ELEVBQUlHLE9BQU96QixPQUNYN0MsRUFBRUMsS0FBS3VELGVBQWlCTixHQUFJQSxFQUFJUSxVQUFVLEdBQVMsU0FBVWEsR0FDekRKLEVBQUkzRCxLQUFLK0QsR0FDVEYsRUFBR0csU0FBUyxVQUFZSCxFQUFHSSxZQUFZLFVBQVlKLEVBQUdLLFNBQVMsWUFHbkVMLEVBQUdHLFNBQVMsVUFBWUgsRUFBR0ksWUFBWSxVQUFZSixFQUFHSyxTQUFTLFVBV3ZFLFFBQVNDLDhCQUE2QjFCLEVBQUdDLEdBQ3JDRCxFQUFFM0MsZ0JBQ0YsSUFBSTRELEdBQUtsRSxFQUFFLDRCQUE4QmtELEVBQ3pDbEQsR0FBRTRFLE1BQ0V0QixLQUFNLE9BQ05DLElBQUt2RCxFQUFFLG9DQUFvQzZFLEtBQUssVUFDaERwQixLQUFNekQsRUFBRSxvQ0FBb0M4RSxZQUM1Q0MsU0FBVSxPQUNWQyxRQUFTLFNBQWlCdkIsR0FDdEJ6RCxFQUFFNEUsTUFDRXRCLEtBQU0sTUFDTkMsSUFBSyw0Q0FDTHdCLFNBQVUsT0FDVkMsUUFBUyxTQUFpQkMsR0FDdEIsR0FBb0IsSUFBaEJBLEVBQVEzQixLQUFZLENBQ3BCLEdBQUk0QixJQUFRQyxhQUFlLEVBQU1DLE9BQVMsRUFBT0MsYUFBZSxFQUFNQyxhQUFlLEVBQU9DLGNBQWlCLG1CQUFvQkMsbUJBQXFCLEVBQU1DLGFBQWdCLEVBQUdDLGFBQWdCLElBQU1DLFFBQVcsS0FBT0MsZ0JBQW1CLElBQU1DLFFBQVcsV0FDblA3RixFQUFFLG9CQUFvQmlCLE9BQU9qQixFQUFFLHNCQUV2QyxRQUFRaUYsRUFBUTNCLE1BQ1osSUFBSyxRQUNEd0MsT0FBT0MsTUFBTWQsRUFBUWUsUUFBUyxHQUFJZCxFQUNsQyxNQUNKLEtBQUssVUFDRFksT0FBT2QsUUFBUUMsRUFBUWUsUUFBUyxHQUFJZCxJQUloRGhCLEVBQUcxRCxLQUFLaUQsU0FhNUIsUUFBU3dDLHdCQUF1QmhELEVBQUdDLEdBQy9CRCxFQUFFM0MsaUJBRUZOLEVBQUVxRCxNQUNFQyxLQUFNLE9BQ05DLElBQUsyQyxrQkFDTHpDLE1BQVFQLEdBQUlBLEdBQ1pTLFVBQVcscUNBQ1hDLE1BQU0sRUFDTmhCLFNBQVMsRUFDVGlCLFVBQVUsSUFDWEMsS0FBSyxXQUNKOUQsRUFBRSx1Q0FBdUNnRSxNQUFNLFVBT3ZELFFBQVNtQyxxQ0FDTG5HLEVBQUUsK0JBQStCb0csU0FDN0JDLFNBQVNDLFlBQVksVUFDckJ0RyxFQUFFLHVDQUF1Q3VHLE9BQU8sT0FBUSxXQUNwRHZHLEVBQUV3RyxNQUFNL0IsWUFBWSxZQUV4QmdDLFdBQVcsV0FDUHpHLEVBQUUsdUNBQXVDMEcsUUFBUSxPQUFRLFdBQ3JEMUcsRUFBRXdHLE1BQU05QixTQUFTLGFBRXRCLE1BSVgsUUFBU3ZELGlCQUNMbkIsRUFBRXFELE1BQ0VDLEtBQU0sT0FDTkMsSUFBS29ELGdCQUFrQjdFLE9BQU9DLFNBQVNJLE9BQ3ZDd0IsVUFBVyxTQUNYaUQsT0FBTyxFQUNQQyxPQUFPLEVBQ1BqRCxNQUFNLElBQ1BFLEtBQUssV0FDSjVDLGFBQWMsRUFDZDRGLGVBUVIsUUFBU3JGLG1CQUFrQmdDLEdBQ3ZCekQsRUFBRXFELE1BQ0VDLEtBQU0sT0FDTkMsSUFBS3dELG9CQUFzQmpGLE9BQU9DLFNBQVNJLE9BQzNDd0IsVUFBVyxVQUNYaUQsT0FBTyxFQUNQQyxPQUFPLEVBQ1BqRCxNQUFNLElBQ1BFLEtBQUssV0FDSnRDLGlCQUFrQixJQUkxQixRQUFTd0YsMEJBQXlCL0QsRUFBR1UsR0FDakMsR0FBSXNELEdBQWdCakgsRUFBRWlELEdBQUdRLEtBQUssY0FDOUJ6RCxHQUFFNEUsTUFDRXNDLE9BQVEsT0FDUjNELElBQUs0RCx1QkFDTDFELE1BQVEyRCxZQUFhSCxHQUNyQkksWUFBYSxtREFDYnJDLFFBQVMsU0FBaUJ2QixHQUN0QkUsRUFBVW5ELEtBQUtpRCxHQUNmekQsRUFBRWlELEdBQUdRLEtBQUssWUFBWSxHQUN0QnpELEVBQUVpRCxHQUFHUSxLQUFLLFlBQVksTUEzVWxDLEdBQUlOLGVBQWUsRUFDZmpDLGFBQWMsRUFDZE0saUJBQWtCLEVBQ2xCakIsU0FBVSxFQUNWYyxhQUFjLENBRWxCckIsR0FBRXFHLFVBQVVpQixLQUFLLDhEQUErRCxXQUM1RW5FLGFBQWVrRCxTQUFTa0IsWUFBY2xCLFNBQVNtQixlQUFpQm5CLFNBQVNvQixxQkFHN0V6SCxFQUFFcUcsVUFBVXFCLE1BQU0sU0FBVTFILEdBQ3hCQSxFQUFFLFFBQVEySCxHQUFHLFFBQVMsWUFBYSxTQUFVMUUsR0FDekNsRCxxQkFFSkMsRUFBRSxRQUFRMkgsR0FBRyxRQUFTLFlBQWEsU0FBVTFFLEdBQy9CakQsRUFBRXdHLE1BQU14RixTQUFTb0QsS0FBSyx3QkFDNUJoQixRQUFRLFdBRWhCcEQsRUFBRSxRQUFRMkgsR0FBRyxRQUFTLFlBQWEsU0FBVTFFLEdBQy9CakQsRUFBRXdHLE1BQU14RixTQUFTb0QsS0FBSyx3QkFDNUJoQixRQUFRLFdBR2hCcEQsRUFBRSxRQUFRMkgsR0FBRyxRQUFTLGNBQWUsU0FBVTFFLEdBRTNDLEdBQUkyRSxHQUFTNUgsRUFBRXdHLE1BQ1hxQixFQUFVRCxFQUFPeEQsS0FBSyxhQUN0QjBELEVBQVVGLEVBQU94RCxLQUFLLGFBRXRCVCxFQUFZaUUsRUFBTzVHLFNBQVNvRCxLQUFLLGtCQUNqQzJELEVBQXFCL0gsRUFBRXdHLE1BQU0vQyxLQUFLLFlBQ2xDbEQsRUFBVVAsRUFBRXdHLE1BQU0vQyxLQUFLLGFBRUEsSUFBdkJzRSxJQUNBZix5QkFBeUJSLEtBQU03QyxHQUMvQmtFLEVBQVFySCxLQUFLd0gsY0FDYkYsRUFBUXJELFlBQVksa0JBQWtCQyxTQUFTLHFCQUduQyxJQUFabkUsSUFBMkMsSUFBdkJ3SCxHQUNwQkYsRUFBUXJILEtBQUt5SCxhQUNidEUsRUFBVStDLFVBQ1ZrQixFQUFPbkUsS0FBSyxZQUFZLEdBQ3hCcUUsRUFBUXJELFlBQVksbUJBQW1CQyxTQUFTLG9CQUM3QixJQUFabkUsSUFBNEMsSUFBdkJ3SCxJQUM1QkYsRUFBUXJILEtBQUt3SCxjQUNickUsRUFBVTRDLFNBQ1ZxQixFQUFPbkUsS0FBSyxZQUFZLEdBQ3hCcUUsRUFBUXJELFlBQVksa0JBQWtCQyxTQUFTLHVCQUk3QixJQUF0QndELG1CQUNBbEksRUFBRSxlQUFlbUkiLCJmaWxlIjoiY2FyLXRyYW5zcG9ydGVyL2luZGV4LmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogZ2xvYmFsIFRFWFRfSElERV9NQVAsIFRFWFRfU0hPV19NQVAsIGFjdGlvblByZXZpZXcsIGFjdGlvblByZXZpZXdMaW5rICovXHJcblxyXG52YXIgaXNGdWxsU2NyZWVuID0gZmFsc2U7XHJcbnZhciBtYXBSZW5kZXJlZCA9IGZhbHNlO1xyXG52YXIgZmlsdGVyc1JlbmRlcmVkID0gZmFsc2U7XHJcbnZhciBtYXBPcGVuID0gZmFsc2UsIGZpbHRlcnNPcGVuID0gZmFsc2U7XHJcblxyXG4kKGRvY3VtZW50KS5iaW5kKCd3ZWJraXRmdWxsc2NyZWVuY2hhbmdlIG1vemZ1bGxzY3JlZW5jaGFuZ2UgZnVsbHNjcmVlbmNoYW5nZScsIGZ1bmN0aW9uKCkge1xyXG4gICAgaXNGdWxsU2NyZWVuID0gZG9jdW1lbnQuZnVsbFNjcmVlbiB8fCBkb2N1bWVudC5tb3pGdWxsU2NyZWVuIHx8IGRvY3VtZW50LndlYmtpdElzRnVsbFNjcmVlbjtcclxufSk7XHJcblxyXG4kKGRvY3VtZW50KS5yZWFkeShmdW5jdGlvbigkKXtcclxuICAgICQoJ2JvZHknKS5vbignY2xpY2snLCAnLm1hcC1saW5rJywgZnVuY3Rpb24oZSkge1xyXG4gICAgICAgIGxvZ09wZW5NYXBBY3Rpb24oKTtcclxuICAgIH0pO1xyXG4gICAgJCgnYm9keScpLm9uKCdjbGljaycsICcuYnRuLXRleHQnLCBmdW5jdGlvbihlKSB7XHJcbiAgICAgICAgdmFyIGJ0biA9ICQodGhpcykucGFyZW50KCkuZmluZCgnaW5wdXRbdHlwZT1cXCdidXR0b25cXCddJyk7XHJcbiAgICAgICAgYnRuLnRyaWdnZXIoJ2NsaWNrJyk7XHJcbiAgICB9KTtcclxuICAgICQoJ2JvZHknKS5vbignY2xpY2snLCAnLmJ0bi1pY29uJywgZnVuY3Rpb24oZSkge1xyXG4gICAgICAgIHZhciBidG4gPSAkKHRoaXMpLnBhcmVudCgpLmZpbmQoJ2lucHV0W3R5cGU9XFwnYnV0dG9uXFwnXScpO1xyXG4gICAgICAgIGJ0bi50cmlnZ2VyKCdjbGljaycpO1xyXG4gICAgfSk7XHJcblxyXG4gICAgJCgnYm9keScpLm9uKCdjbGljaycsICcubWFwLWJ1dHRvbicsIGZ1bmN0aW9uKGUpe1xyXG5cclxuICAgICAgICB2YXIgYnV0dG9uID0gJCh0aGlzKTtcclxuICAgICAgICB2YXIgYnRuVGV4dCA9IGJ1dHRvbi5maW5kKCcuYnRuLXRleHQnKTtcclxuICAgICAgICB2YXIgYnRuSWNvbiA9IGJ1dHRvbi5maW5kKCcuYnRuLWljb24nKTtcclxuXHJcbiAgICAgICAgdmFyIGNvbnRhaW5lciA9ICBidXR0b24ucGFyZW50KCkuZmluZCgnLm1hcC1jb250YWluZXInKTtcclxuICAgICAgICB2YXIgY29udGFjdE1hcFJlbmRlcmVkID0gJCh0aGlzKS5kYXRhKCdyZW5kZXJlZCcpO1xyXG4gICAgICAgIHZhciBtYXBPcGVuID0gJCh0aGlzKS5kYXRhKCdtYXAtb3BlbicpO1xyXG5cclxuICAgICAgICBpZiAoY29udGFjdE1hcFJlbmRlcmVkID09PSBmYWxzZSkge1xyXG4gICAgICAgICAgICByZW5kZXJMb2FkQ29udGFjdE1hcEFqYXgodGhpcywgY29udGFpbmVyKTtcclxuICAgICAgICAgICAgYnRuVGV4dC5odG1sKGNsb3NlTWFwVGV4dCk7XHJcbiAgICAgICAgICAgIGJ0bkljb24ucmVtb3ZlQ2xhc3MoJ2ZhLXBsdXMtY2lyY2xlJykuYWRkQ2xhc3MoJ2ZhLW1pbnVzLWNpcmNsZScpO1xyXG4gICAgICAgIH1cclxuXHJcbiAgICAgICAgaWYgKG1hcE9wZW4gPT09IHRydWUgJiYgY29udGFjdE1hcFJlbmRlcmVkID09PSB0cnVlKSB7XHJcbiAgICAgICAgICAgIGJ0blRleHQuaHRtbChvcGVuTWFwVGV4dCk7XHJcbiAgICAgICAgICAgIGNvbnRhaW5lci5mYWRlT3V0KCk7XHJcbiAgICAgICAgICAgIGJ1dHRvbi5kYXRhKCdtYXAtb3BlbicsIGZhbHNlKTtcclxuICAgICAgICAgICAgYnRuSWNvbi5yZW1vdmVDbGFzcygnZmEtbWludXMtY2lyY2xlJykuYWRkQ2xhc3MoJ2ZhLXBsdXMtY2lyY2xlJyk7XHJcblxyXG4gICAgICAgIH0gZWxzZSBpZiAobWFwT3BlbiA9PT0gZmFsc2UgJiYgY29udGFjdE1hcFJlbmRlcmVkID09PSB0cnVlKSB7XHJcbiAgICAgICAgICAgIGJ0blRleHQuaHRtbChjbG9zZU1hcFRleHQpO1xyXG4gICAgICAgICAgICBjb250YWluZXIuZmFkZUluKCk7XHJcbiAgICAgICAgICAgIGJ1dHRvbi5kYXRhKCdtYXAtb3BlbicsIHRydWUpO1xyXG4gICAgICAgICAgICBidG5JY29uLnJlbW92ZUNsYXNzKCdmYS1wbHVzLWNpcmNsZScpLmFkZENsYXNzKCdmYS1taW51cy1jaXJjbGUnKTtcclxuICAgICAgICB9XHJcblxyXG4gICAgfSk7XHJcblxyXG4gICAgaWYgKG5lZWRUb09wZW5GaWx0ZXJzID09PSB0cnVlKSB7XHJcbiAgICAgICAgJCgnI2ZpbHRlci1idG4nKS5jbGljaygpO1xyXG4gICAgfVxyXG59KTtcclxuXHJcbi8qKlxyXG4gKiBTZW5kcyByZXF1ZXN0IHRvIGJhY2tlbmQgdG8gcmVnaXN0ZXIgbWFwIG9wZW4gYWN0aW9uIGZvciBzdGF0aXN0aWNzXHJcbiAqL1xyXG5mdW5jdGlvbiBsb2dPcGVuTWFwQWN0aW9uKCkge1xyXG4gICAgJC5wb3N0KGFjdGlvbkxvZ1RyYW5zcG9ydGVyTWFwT3Blbiwge30pO1xyXG59XHJcblxyXG4vKipcclxuICogQ2hhbmdlcyBtYXAgY29sbGFwc2UgYnV0dG9uIHRleHQgZGVwZW5kaW5nIG9uIHdoZXRoZXIgbWFwIGlzIGV4cGFuZGVkIG9yIG5vdFxyXG4gKlxyXG4gKiBAcGFyYW0ge29iamVjdH0gZWxlbWVudCBUaGlzIGVsZW1lbnRcclxuICovXHJcbmZ1bmN0aW9uIGNoYW5nZU1hcENvbGxhcHNlQnV0dG9uVGV4dChlbGVtZW50LGV2ZW50KSB7XHJcbiAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xyXG4gICAgaWYgKG1hcE9wZW4pIHtcclxuICAgICAgICB2YXIgaHRtbCA9ICc8aSBjbGFzcz1cImZhIGZhLXBsdXMtY2lyY2xlIGJ0bi1pY29uIG1hcC1saW5rXCI+PC9pPiA8c3BhbiBjbGFzcz1cImJ0bi10ZXh0IG1hcC1saW5rXCI+JyArIFRFWFRfU0hPV19NQVAgKyAnPC9zcGFuPic7XHJcbiAgICB9IGVsc2Uge1xyXG4gICAgICAgIHZhciBodG1sID0gJzxpIGNsYXNzPVwiZmEgZmEtbWludXMtY2lyY2xlIG1hcmdpbi1yaWdodC01IGJ0bi1pY29uXCI+PC9pPjxzcGFuIGNsYXNzPVwiYnRuLXRleHQgY29sb3ItYmxhY2tcIj4nICsgVEVYVF9ISURFX01BUCArICc8L3NwYW4+JztcclxuICAgIH1cclxuXHJcbiAgICB2YXIgc2libGluZ0VsZW1zID0gJChlbGVtZW50KS5zaWJsaW5ncygpO1xyXG4gICAgc2libGluZ0VsZW1zLmVhY2goKGluZGV4LCBpdGVtKSA9PiB7XHJcbiAgICAgICAgaXRlbS5yZW1vdmUoKTtcclxuICAgIH0pO1xyXG4gICAgJChlbGVtZW50KS5wYXJlbnQoKS5hcHBlbmQoaHRtbCk7XHJcbiAgICBpZiAobWFwUmVuZGVyZWQgPT09IGZhbHNlKSB7XHJcbiAgICAgICAgcmVuZGVyTWFwQWpheCgpO1xyXG4gICAgfVxyXG4gICAgbWFwT3BlbiA9ICFtYXBPcGVuO1xyXG59XHJcblxyXG5mdW5jdGlvbiBjaGFuZ2VGaWx0ZXJzQ29sbGFwc2VCdXR0b25UZXh0KGVsZW1lbnQsZXZlbnQpIHtcclxuICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XHJcbiAgICBpZiAoZmlsdGVyc09wZW4pIHtcclxuICAgICAgICB2YXIgaHRtbCA9ICc8aSBjbGFzcz1cImZhIGZhLXBsdXMtY2lyY2xlIGJ0bi1pY29uIG1hcmdpbi1yaWdodC01XCI+PC9pPjxzcGFuIGNsYXNzPVwiYnRuLXRleHRcIj4nICsgVEVYVF9TSE9XX0ZJTFRFUlMgKyAnPC9zcGFuPic7XHJcbiAgICB9IGVsc2Uge1xyXG4gICAgICAgIHZhciBodG1sID0gJzxpIGNsYXNzPVwiZmEgZmEtbWludXMtY2lyY2xlIG1hcmdpbi1yaWdodC01IGJ0bi1pY29uXCI+PC9pPjxzcGFuIGNsYXNzPVwiYnRuLXRleHQgY29sb3ItYmxhY2tcIj4nICsgVEVYVF9ISURFX0ZJTFRFUlMgKyAnPC9zcGFuPic7XHJcbiAgICB9XHJcbiAgICB2YXIgc2libGluZ0VsZW1zID0gJChlbGVtZW50KS5zaWJsaW5ncygpO1xyXG4gICAgc2libGluZ0VsZW1zLmVhY2goKGluZGV4LCBpdGVtKSA9PiB7XHJcbiAgICAgICAgaXRlbS5yZW1vdmUoKTtcclxuICAgIH0pO1xyXG5cclxuXHJcbiAgICAkKGVsZW1lbnQpLnBhcmVudCgpLmFwcGVuZChodG1sKTtcclxuICAgIGlmIChmaWx0ZXJzUmVuZGVyZWQgPT09IGZhbHNlKSB7XHJcbiAgICAgICAgcmVuZGVyRmlsdGVyc0FqYXgoKTtcclxuICAgIH1cclxuICAgIGZpbHRlcnNPcGVuID0gIWZpbHRlcnNPcGVuO1xyXG59XHJcblxyXG4vKipcclxuICogRmlsdGVycyBjYXIgdHJhbnNwb3J0ZXJzIG1hcCBieSB1bmxvYWQgY2l0eSBvciBjb3VudHJ5XHJcbiAqXHJcbiAqIEBwYXJhbSB7b2JqZWN0fSBlbGVtZW50IFRoaXMgb2JqZWN0XHJcbiAqL1xyXG5mdW5jdGlvbiBmaWx0ZXJCeVVubG9hZENpdHkoZWxlbWVudCkge1xyXG4gICAgdmFyIHVubG9hZENpdHlJZCA9ICQoZWxlbWVudCkudmFsKCk7XHJcbiAgICB2YXIgcGF0aE5hbWUgPSB3aW5kb3cubG9jYXRpb24ucGF0aG5hbWU7XHJcbiAgICB2YXIgcXVlcnlQYXJhbXMgPSByZXBsYWNlUXVlcnlQYXJhbSgndW5sb2FkQ2l0eUlkJywgdW5sb2FkQ2l0eUlkLCB3aW5kb3cubG9jYXRpb24uc2VhcmNoKTtcclxuICAgIHdpbmRvdy5oaXN0b3J5LnB1c2hTdGF0ZShudWxsLCAnJywgcGF0aE5hbWUgKyBxdWVyeVBhcmFtcyk7XHJcbiAgICBsb2NhdGlvbi5yZWxvYWQoKTtcclxufVxyXG5cclxuLyoqXHJcbiAqIFJlcGxhY2VzIHF1ZXJ5IHBhcmFtc1xyXG4gKlxyXG4gKiBAc2VlIHtAbGluayBodHRwOi8vc3RhY2tvdmVyZmxvdy5jb20vYS8xOTQ3MjQxMC81NzQ3ODY3fVxyXG4gKiBAcGFyYW0ge3N0cmluZ30gcGFyYW1cclxuICogQHBhcmFtIHtzdHJpbmd9IHZhbHVlXHJcbiAqIEBwYXJhbSB7c3RyaW5nfSBzZWFyY2hcclxuICogQHJldHVybnMge3N0cmluZ31cclxuICovXHJcbmZ1bmN0aW9uIHJlcGxhY2VRdWVyeVBhcmFtKHBhcmFtLCB2YWx1ZSwgc2VhcmNoKSB7XHJcbiAgICB2YXIgcmVnZXggPSBuZXcgUmVnRXhwKFwiKFs/OyZdKVwiICsgcGFyYW0gKyBcIlteJjtdKls7Jl0/XCIpO1xyXG4gICAgdmFyIHF1ZXJ5ID0gc2VhcmNoLnJlcGxhY2UocmVnZXgsIFwiJDFcIikucmVwbGFjZSgvJiQvLCAnJyk7XHJcblxyXG4gICAgaWYgKHZhbHVlID09PSAnJykge1xyXG4gICAgICAgIHJldHVybiBxdWVyeTtcclxuICAgIH1cclxuXHJcbiAgICByZXR1cm4gKHF1ZXJ5Lmxlbmd0aCA+IDIgPyBxdWVyeSArIFwiJlwiIDogXCI/XCIpICsgKHZhbHVlID8gcGFyYW0gKyBcIj1cIiArIHZhbHVlIDogJycpO1xyXG59XHJcblxyXG4vKipcclxuICogQ2hhbmdlcyBlbnRyaWVzIHBlciBwYWdlIHNpemVcclxuICpcclxuICogQHBhcmFtIHtvYmplY3R9IGVsZW1lbnQgVGhpcyBvYmplY3RcclxuICovXHJcbmZ1bmN0aW9uIGNoYW5nZVBhZ2VTaXplKGVsZW1lbnQpIHtcclxuICAgIHZhciBwYWdlU2l6ZSA9ICQoZWxlbWVudCkudmFsKCk7XHJcbiAgICB2YXIgcGF0aE5hbWUgPSB3aW5kb3cubG9jYXRpb24ucGF0aG5hbWU7XHJcbiAgICB2YXIgcXVlcnlQYXJhbXMgPSByZXBsYWNlUXVlcnlQYXJhbSgncGFnZVNpemUnLCBwYWdlU2l6ZSwgd2luZG93LmxvY2F0aW9uLnNlYXJjaCk7XHJcbiAgICB3aW5kb3cuaGlzdG9yeS5wdXNoU3RhdGUobnVsbCwgJycsIHBhdGhOYW1lICsgcXVlcnlQYXJhbXMpO1xyXG4gICAgbG9jYXRpb24ucmVsb2FkKCk7XHJcbn1cclxuXHJcbi8qKlxyXG4gKiBSZW5kZXJzIGNhciB0cmFuc3BvcnRlciBvd25lciBjb250YWN0IGluZm8gcHJldmlld1xyXG4gKlxyXG4gKiBAcGFyYW0ge29iamVjdH0gZSBFdmVudCBvYmplY3RcclxuICogQHBhcmFtIHtudW1iZXJ9IGlkIENhciB0cmFuc3BvcnRlciBJRFxyXG4gKi9cclxuZnVuY3Rpb24gcHJldmlld0NvbnRhY3RJbmZvKGUsIGlkKSB7XHJcbiAgICBlLnByZXZlbnREZWZhdWx0KCk7XHJcbiAgICBpZiAoaXNGdWxsU2NyZWVuKSB7XHJcbiAgICAgICAgJCgnI21hcF9jYW52YXMgZGl2LmdtLXN0eWxlIGJ1dHRvblt0aXRsZT1cIlRvZ2dsZSBmdWxsc2NyZWVuIHZpZXdcIl0nKS50cmlnZ2VyKCdjbGljaycpO1xyXG4gICAgfVxyXG4gICAgJC5wamF4KHtcclxuICAgICAgICB0eXBlOiAnUE9TVCcsXHJcbiAgICAgICAgdXJsOiBhY3Rpb25QcmV2aWV3LFxyXG4gICAgICAgIGRhdGE6IHtcclxuICAgICAgICAgICAgaWQ6IGlkLFxyXG4gICAgICAgICAgICBzaG93SW5mbzogdHJ1ZVxyXG4gICAgICAgIH0sXHJcbiAgICAgICAgY29udGFpbmVyOiAnI2NvbnRhY3QtaW5mby1wcmV2aWV3LXBqYXgnLFxyXG4gICAgICAgIHB1c2g6IGZhbHNlLFxyXG4gICAgICAgIHJlcGxhY2U6IGZhbHNlLFxyXG4gICAgICAgIHNjcm9sbFRvOiBmYWxzZVxyXG4gICAgfSkuZG9uZShmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgc2hvd0NvbnRhY3RJbmZvUHJldmlld01vZGFsKCk7XHJcbiAgICB9KTtcclxufVxyXG5cclxuLyoqXHJcbiAqIFNob3dzIGNvbnRhY3QgaW5mbyBwcmV2aWV3IG1vZGFsXHJcbiAqL1xyXG5mdW5jdGlvbiBzaG93Q29udGFjdEluZm9QcmV2aWV3TW9kYWwoKSB7XHJcbiAgICAkKCcjY29udGFjdC1pbmZvLXByZXZpZXctbW9kYWwnKS5tb2RhbCgnc2hvdycpO1xyXG59XHJcblxyXG4vKipcclxuICogU2hvd3Mgb3IgaGlkZXMgY2FyIHRyYW5zcG9ydGVyIHByZXZpZXdcclxuICpcclxuICogQHBhcmFtIHtvYmplY3R9IGUgRXZlbnQgb2JqZWN0XHJcbiAqIEBwYXJhbSB7bnVtYmVyfSBpZCBDYXIgdHJhbnNwb3J0ZXIgSURcclxuICovXHJcbmZ1bmN0aW9uIGNvbGxhcHNlQ2FyVHJhbnNwb3J0ZXJQcmV2aWV3KGUsIGlkKSB7XHJcbiAgICBlLnByZXZlbnREZWZhdWx0KCk7XHJcblxyXG4gICAgdmFyIHRkID0gJCgnI2Nhci10cmFuc3BvcnRlci1wcmV2aWV3LScgKyBpZCk7XHJcbiAgICB2YXIgZGl2ID0gdGQuZmluZCgnLmNvbnRlbnQnKTtcclxuICAgIHZhciB0ciA9IHRkLnBhcmVudCgpO1xyXG5cclxuICAgIGlmIChkaXYudGV4dCgpLmxlbmd0aCA9PT0gMCkge1xyXG4gICAgICAgICQucG9zdChhY3Rpb25QcmV2aWV3LCB7aWQ6IGlkLCBzaG93SW5mbzogZmFsc2V9LCBmdW5jdGlvbiAoY29udGVudCkge1xyXG4gICAgICAgICAgICBkaXYuaHRtbChjb250ZW50KTtcclxuICAgICAgICAgICAgdHIuaGFzQ2xhc3MoJ2hpZGRlbicpID8gdHIucmVtb3ZlQ2xhc3MoJ2hpZGRlbicpIDogdHIuYWRkQ2xhc3MoJ2hpZGRlbicpO1xyXG4gICAgICAgIH0pO1xyXG4gICAgfSBlbHNlIHtcclxuICAgICAgICB0ci5oYXNDbGFzcygnaGlkZGVuJykgPyB0ci5yZW1vdmVDbGFzcygnaGlkZGVuJykgOiB0ci5hZGRDbGFzcygnaGlkZGVuJyk7XHJcbiAgICB9XHJcbn1cclxuXHJcbi8qKlxyXG4gKiBDaGVja3MgY3JlZGl0IGNvZGUgYW5kIHNob3dzIGNhciB0cmFuc3BvcnRlclxyXG4gKiBwcmV2aWV3IGFuZCBUb2FzdCBNZXNzYWdlcyBkZXBlbmRlbmQgb24gY3JlZGl0IGNvZGUuXHJcbiAqXHJcbiAqIEBwYXJhbSB7b2JqZWN0fSBlIEV2ZW50IG9iamVjdFxyXG4gKiBAcGFyYW0ge251bWJlcn0gaWQgQ2FyIHRyYW5zcG9ydGVyIElEXHJcbiAqL1xyXG5mdW5jdGlvbiByZWZyZXNoQ2FyVHJhbnNwb3J0ZXJQcmV2aWV3KGUsIGlkKSB7XHJcbiAgICBlLnByZXZlbnREZWZhdWx0KCk7XHJcbiAgICB2YXIgdGQgPSAkKCcjY2FyLXRyYW5zcG9ydGVyLXByZXZpZXctJyArIGlkKTtcclxuICAgICQuYWpheCh7XHJcbiAgICAgICAgdHlwZTogJ1BPU1QnLFxyXG4gICAgICAgIHVybDogJCgnI2Nhci10cmFuc3BvcnRlci1jcmVkaXRjb2RlLWZvcm0nKS5hdHRyKCdhY3Rpb24nKSxcclxuICAgICAgICBkYXRhOiAkKCcjY2FyLXRyYW5zcG9ydGVyLWNyZWRpdGNvZGUtZm9ybScpLnNlcmlhbGl6ZSgpLFxyXG4gICAgICAgIGRhdGFUeXBlOiAnaHRtbCcsXHJcbiAgICAgICAgc3VjY2VzczogZnVuY3Rpb24oZGF0YSkge1xyXG4gICAgICAgICAgICAkLmFqYXgoe1xyXG4gICAgICAgICAgICAgICAgdHlwZTogJ0dFVCcsXHJcbiAgICAgICAgICAgICAgICB1cmw6ICdjYXItdHJhbnNwb3J0ZXIvZ2V0LW1zZ3MtY3JlZGl0Y29kZS1zdGF0ZScsXHJcbiAgICAgICAgICAgICAgICBkYXRhVHlwZTogJ2pzb24nLFxyXG4gICAgICAgICAgICAgICAgc3VjY2VzczogZnVuY3Rpb24obXNnZGF0YSkge1xyXG4gICAgICAgICAgICAgICAgICAgIGlmIChtc2dkYXRhLnR5cGUgIT0gJycpIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgdmFyIGNmZyA9IHsnY2xvc2VCdXR0b24nOnRydWUsJ2RlYnVnJzpmYWxzZSwnbmV3ZXN0T25Ub3AnOnRydWUsJ3Byb2dyZXNzQmFyJzpmYWxzZSwncG9zaXRpb25DbGFzcyc6J3RvYXN0LXRvcC1jZW50ZXInLCdwcmV2ZW50RHVwbGljYXRlcyc6dHJ1ZSwnc2hvd0R1cmF0aW9uJzowLCdoaWRlRHVyYXRpb24nOjEwMDAsJ3RpbWVPdXQnOjQ1MDAwLCdleHRlbmRlZFRpbWVPdXQnOjgwMDAsJ29uU2hvd24nOmZ1bmN0aW9uKCkgeyAkKCcuYWxlcnQtY29udGFpbmVyJykuYXBwZW5kKCQoJyN0b2FzdC1jb250YWluZXInKSk7fX07XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHN3aXRjaChtc2dkYXRhLnR5cGUpIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGNhc2UgJ2Vycm9yJzpcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB0b2FzdHIuZXJyb3IobXNnZGF0YS5tZXNzYWdlLCAnJywgY2ZnKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBicmVhaztcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGNhc2UgJ3N1Y2Nlc3MnOlxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRvYXN0ci5zdWNjZXNzKG1zZ2RhdGEubWVzc2FnZSwgJycsIGNmZyk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgYnJlYWs7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgdGQuaHRtbChkYXRhKTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgfVxyXG4gICAgfSk7XHJcbn1cclxuXHJcbi8qKlxyXG4gKiBTaG93cyBjYXIgdHJhbnNwb3J0ZXIgbGlua1xyXG4gKlxyXG4gKiBAcGFyYW0ge29iamVjdH0gZSBFdmVudCBvYmplY3RcclxuICogQHBhcmFtIHtudW1iZXJ9IGlkIENhciB0cmFuc3BvcnRlciBJRFxyXG4gKi9cclxuZnVuY3Rpb24gc2hvd0NhclRyYW5zcG9ydGVyTGluayhlLCBpZCkge1xyXG4gICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xyXG5cclxuICAgICQucGpheCh7XHJcbiAgICAgICAgdHlwZTogJ1BPU1QnLFxyXG4gICAgICAgIHVybDogYWN0aW9uUHJldmlld0xpbmssXHJcbiAgICAgICAgZGF0YToge2lkOiBpZH0sXHJcbiAgICAgICAgY29udGFpbmVyOiAnI2Nhci10cmFuc3BvcnRlci1saW5rLXByZXZpZXctcGpheCcsXHJcbiAgICAgICAgcHVzaDogZmFsc2UsXHJcbiAgICAgICAgcmVwbGFjZTogZmFsc2UsXHJcbiAgICAgICAgc2Nyb2xsVG86IGZhbHNlXHJcbiAgICB9KS5kb25lKGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAkKCcjY2FyLXRyYW5zcG9ydGVyLWxpbmstcHJldmlldy1tb2RhbCcpLm1vZGFsKCdzaG93Jyk7XHJcbiAgICB9KTtcclxufVxyXG5cclxuLyoqXHJcbiAqIENvcGllcyBjYXIgdHJhbnNwb3J0ZXIgbGluayB0byBjbGlwYm9hcmRcclxuICovXHJcbmZ1bmN0aW9uIGNvcHlDYXJUcmFuc3BvcnRlckxpbmtUb0NsaXBib2FyZCgpIHtcclxuICAgICQoJyNjYXItdHJhbnNwb3J0ZXItbGluay1maWVsZCcpLnNlbGVjdCgpO1xyXG4gICAgaWYgKGRvY3VtZW50LmV4ZWNDb21tYW5kKCdDb3B5JykpIHtcclxuICAgICAgICAkKFwiI2Nhci10cmFuc3BvcnRlci1saW5rLXN1Y2Nlc3MtYWxlcnRcIikuZmFkZUluKCdzbG93JywgZnVuY3Rpb24oKSB7XHJcbiAgICAgICAgICAgICQodGhpcykucmVtb3ZlQ2xhc3MoJ2hpZGRlbicpO1xyXG4gICAgICAgIH0pO1xyXG4gICAgICAgIHNldFRpbWVvdXQoZnVuY3Rpb24oKSB7XHJcbiAgICAgICAgICAgICQoXCIjY2FyLXRyYW5zcG9ydGVyLWxpbmstc3VjY2Vzcy1hbGVydFwiKS5mYWRlT3V0KCdzbG93JywgZnVuY3Rpb24oKSB7XHJcbiAgICAgICAgICAgICAgICAkKHRoaXMpLmFkZENsYXNzKCdoaWRkZW4nKTtcclxuICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgfSwgNTAwMCk7IC8vIDUgc2Vjb25kc1xyXG4gICAgfVxyXG59XHJcblxyXG5mdW5jdGlvbiByZW5kZXJNYXBBamF4KClcclxue1xyXG4gICAgJC5wamF4KHtcclxuICAgICAgICB0eXBlOiAnUE9TVCcsXHJcbiAgICAgICAgdXJsOiBhY3Rpb25SZW5kZXJNYXAgKyB3aW5kb3cubG9jYXRpb24uc2VhcmNoLFxyXG4gICAgICAgIGNvbnRhaW5lcjogJyNDLVQtMycsXHJcbiAgICAgICAgY2FjaGU6IGZhbHNlLFxyXG4gICAgICAgIGFzeW5jOiB0cnVlLFxyXG4gICAgICAgIHB1c2g6IGZhbHNlLFxyXG4gICAgfSkuZG9uZShmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgbWFwUmVuZGVyZWQgPSB0cnVlO1xyXG4gICAgICAgIGxvYWRTY3JpcHQoKTtcclxuICAgIH0pO1xyXG59XHJcblxyXG4vKipcclxuICpcclxuICogQHBhcmFtIGRhdGFcclxuICovXHJcbmZ1bmN0aW9uIHJlbmRlckZpbHRlcnNBamF4KGRhdGEpXHJcbntcclxuICAgICQucGpheCh7XHJcbiAgICAgICAgdHlwZTogJ1BPU1QnLFxyXG4gICAgICAgIHVybDogYWN0aW9uUmVuZGVyRmlsdGVycyArIHdpbmRvdy5sb2NhdGlvbi5zZWFyY2gsXHJcbiAgICAgICAgY29udGFpbmVyOiAnI2ZpbHRlcicsXHJcbiAgICAgICAgY2FjaGU6IGZhbHNlLFxyXG4gICAgICAgIGFzeW5jOiB0cnVlLFxyXG4gICAgICAgIHB1c2g6IGZhbHNlLFxyXG4gICAgfSkuZG9uZShmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgZmlsdGVyc1JlbmRlcmVkID0gdHJ1ZTtcclxuICAgIH0pO1xyXG59XHJcblxyXG5cclxuZnVuY3Rpb24gcmVuZGVyTG9hZENvbnRhY3RNYXBBamF4KGUsIGNvbnRhaW5lcilcclxue1xyXG4gICAgdmFyIHRyYW5zcG9ydGVySUQgPSAkKGUpLmRhdGEoJ3RyYW5zcG9ydGVyJyk7XHJcbiAgICAkLmFqYXgoe1xyXG4gICAgICAgIG1ldGhvZDogJ1BPU1QnLFxyXG4gICAgICAgIHVybDogYWN0aW9uUmVuZGVyQ29udGFjdE1hcCxcclxuICAgICAgICBkYXRhOiB7dHJhbnNwb3J0ZXI6IHRyYW5zcG9ydGVySUR9LFxyXG4gICAgICAgIGNvbnRlbnRUeXBlOiAnYXBwbGljYXRpb24veC13d3ctZm9ybS11cmxlbmNvZGVkOyBjaGFyc2V0PVVURi04JyxcclxuICAgICAgICBzdWNjZXNzOiBmdW5jdGlvbihkYXRhKSB7XHJcbiAgICAgICAgICAgIGNvbnRhaW5lci5odG1sKGRhdGEpO1xyXG4gICAgICAgICAgICAkKGUpLmRhdGEoJ3JlbmRlcmVkJywgdHJ1ZSk7XHJcbiAgICAgICAgICAgICQoZSkuZGF0YSgnbWFwLW9wZW4nLCB0cnVlKVxyXG4gICAgICAgIH1cclxuICAgIH0pO1xyXG59Il19