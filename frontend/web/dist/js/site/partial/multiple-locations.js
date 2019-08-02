"use strict";function archiveLocations(o){$.each(o,function(o,t){t.id in archivedLocations||(archivedLocations[t.id]=t)})}function isDirection(o){return"undefined"!==_typeof(o.directionId)&&!1!==o.directionId}function getDirectionSuggestion(o){return"<div class='direction'>"+o+"</div>"}function getSimpleSuggestion(o){return"<div>"+o.name+"<div class='pull-right'><span class='map-text'data-lat='"+o.location.lat+"' data-lon='"+o.location.lon+"' data-zoom='"+o.zoom+"' data-toggle='popover' data-placement='top' data-content='<div class=\"load-city-map\"></div>'> "+translateMap+"</span></div></div>"}function getLocationName(o){if(""!=$(o).attr("text"))return o.text;var t="";return $.each(archivedLocations,function(n,i){o.id==n&&(isDirection(i)?(removeLocationOption(loadId,n),updateLocation(loadId,i.popularId,i.popularName),updateLocation(unloadId,i.directionId,i.directionName)):t=i.name)}),t}function removeLocationOption(o,t){$("#"+o).find('option[value="'+t+'"]').remove()}function updateLocation(o,t,n){if(!isLocationExists(o,t)){var i="#"+o,e=null===$(i).val()?[]:$(i).val(),a=$("<option></option>").attr("value",t).html(n);$(i).append(a),e.push(t),$(i).val(e).change()}}function isLocationExists(o,t){return $("#"+o).find('option[value="'+t+'"]').length>0}function removeLocation(o){var t=getLocationsBeforeRemove("#"+o),n=$("#"+o).val(),i=$(t).not(n).get();$.each(i,function(t,n){removeLocationOption(o,n)})}function getLocationsBeforeRemove(o){var t=[];return $(o+" option").each(function(){t.push($(this).val())}),t}var _typeof2="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(o){return typeof o}:function(o){return o&&"function"==typeof Symbol&&o.constructor===Symbol&&o!==Symbol.prototype?"symbol":typeof o},_typeof="function"==typeof Symbol&&"symbol"===_typeof2(Symbol.iterator)?function(o){return void 0===o?"undefined":_typeof2(o)}:function(o){return o&&"function"==typeof Symbol&&o.constructor===Symbol&&o!==Symbol.prototype?"symbol":void 0===o?"undefined":_typeof2(o)},archivedLocations={};
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInNpdGUvcGFydGlhbC9tdWx0aXBsZS1sb2NhdGlvbnMuanMiXSwibmFtZXMiOlsiYXJjaGl2ZUxvY2F0aW9ucyIsImxvY2F0aW9ucyIsIiQiLCJlYWNoIiwia2V5IiwibG9jYXRpb24iLCJpZCIsImFyY2hpdmVkTG9jYXRpb25zIiwiaXNEaXJlY3Rpb24iLCJkYXRhIiwiX3R5cGVvZiIsImRpcmVjdGlvbklkIiwiZ2V0RGlyZWN0aW9uU3VnZ2VzdGlvbiIsIm5hbWUiLCJnZXRTaW1wbGVTdWdnZXN0aW9uIiwibGF0IiwibG9uIiwiem9vbSIsInRyYW5zbGF0ZU1hcCIsImdldExvY2F0aW9uTmFtZSIsImN1cnJlbnRMb2NhdGlvbiIsImF0dHIiLCJ0ZXh0IiwiYXJjaGl2ZWRMb2NhdGlvbklkIiwiYXJjaGl2ZWRMb2NhdGlvbiIsInJlbW92ZUxvY2F0aW9uT3B0aW9uIiwibG9hZElkIiwidXBkYXRlTG9jYXRpb24iLCJwb3B1bGFySWQiLCJwb3B1bGFyTmFtZSIsInVubG9hZElkIiwiZGlyZWN0aW9uTmFtZSIsImVsZW1lbnRJZCIsImxvY2F0aW9uSWQiLCJmaW5kIiwicmVtb3ZlIiwibG9jYXRpb25OYW1lIiwiaXNMb2NhdGlvbkV4aXN0cyIsInNlbGVjdG9yIiwic2VsZWN0ZWRMb2NhdGlvbnMiLCJ2YWwiLCJvcHRpb24iLCJodG1sIiwiYXBwZW5kIiwicHVzaCIsImNoYW5nZSIsImxlbmd0aCIsInJlbW92ZUxvY2F0aW9uIiwib2xkTG9jYXRpb25zIiwiZ2V0TG9jYXRpb25zQmVmb3JlUmVtb3ZlIiwiY3VycmVudExvY2F0aW9ucyIsImxvY2F0aW9uc1RvUmVtb3ZlIiwibm90IiwiZ2V0IiwidGhpcyIsIl90eXBlb2YyIiwiU3ltYm9sIiwiaXRlcmF0b3IiLCJvYmoiLCJjb25zdHJ1Y3RvciIsInByb3RvdHlwZSJdLCJtYXBwaW5ncyI6IkFBQUEsWUFtQkEsU0FBU0Esa0JBQWlCQyxHQUN4QkMsRUFBRUMsS0FBS0YsRUFBVyxTQUFVRyxFQUFLQyxHQUN6QkEsRUFBU0MsS0FBTUMscUJBQ25CQSxrQkFBa0JGLEVBQVNDLElBQU1ELEtBV3ZDLFFBQVNHLGFBQVlDLEdBQ25CLE1BQXlFLGNBQWxFQyxRQUFRRCxFQUFLRSxlQUErRyxJQUFyQkYsRUFBS0UsWUFTckgsUUFBU0Msd0JBQXVCQyxHQUM5QixNQUFPLDBCQUE0QkEsRUFBTyxTQVM1QyxRQUFTQyxxQkFBb0JMLEdBQzNCLE1BQU8sUUFBVUEsRUFBS0ksS0FBTywyREFBdUVKLEVBQUtKLFNBQVNVLElBQU0sZUFBc0JOLEVBQUtKLFNBQVNXLElBQU0sZ0JBQXVCUCxFQUFLUSxLQUFPLG9HQUFxSEMsYUFBZSxzQkFTM1UsUUFBU0MsaUJBQWdCQyxHQUN2QixHQUF1QyxJQUFuQ2xCLEVBQUVrQixHQUFpQkMsS0FBSyxRQUMxQixNQUFPRCxHQUFnQkUsSUFHekIsSUFBSVQsR0FBTyxFQVlYLE9BWEFYLEdBQUVDLEtBQUtJLGtCQUFtQixTQUFVZ0IsRUFBb0JDLEdBQ2xESixFQUFnQmQsSUFBTWlCLElBQ3BCZixZQUFZZ0IsSUFDZEMscUJBQXFCQyxPQUFRSCxHQUM3QkksZUFBZUQsT0FBUUYsRUFBaUJJLFVBQVdKLEVBQWlCSyxhQUNwRUYsZUFBZUcsU0FBVU4sRUFBaUJiLFlBQWFhLEVBQWlCTyxnQkFFeEVsQixFQUFPVyxFQUFpQlgsUUFJdkJBLEVBU1QsUUFBU1ksc0JBQXFCTyxFQUFXQyxHQUN2Qy9CLEVBQUUsSUFBTThCLEdBQVdFLEtBQUssaUJBQW1CRCxFQUFhLE1BQU1FLFNBVWhFLFFBQVNSLGdCQUFlSyxFQUFXQyxFQUFZRyxHQUM3QyxJQUFLQyxpQkFBaUJMLEVBQVdDLEdBQWEsQ0FDNUMsR0FBSUssR0FBVyxJQUFNTixFQUNqQk8sRUFBMEMsT0FBdEJyQyxFQUFFb0MsR0FBVUUsU0FBc0J0QyxFQUFFb0MsR0FBVUUsTUFDbEVDLEVBQVN2QyxFQUFFLHFCQUFxQm1CLEtBQUssUUFBU1ksR0FBWVMsS0FBS04sRUFDbkVsQyxHQUFFb0MsR0FBVUssT0FBT0YsR0FDbkJGLEVBQWtCSyxLQUFLWCxHQUN2Qi9CLEVBQUVvQyxHQUFVRSxJQUFJRCxHQUFtQk0sVUFXdkMsUUFBU1Isa0JBQWlCTCxFQUFXQyxHQUNuQyxNQUFPL0IsR0FBRSxJQUFNOEIsR0FBV0UsS0FBSyxpQkFBbUJELEVBQWEsTUFBTWEsT0FBUyxFQVFoRixRQUFTQyxnQkFBZWYsR0FDdEIsR0FBSWdCLEdBQWVDLHlCQUF5QixJQUFNakIsR0FDOUNrQixFQUFtQmhELEVBQUUsSUFBTThCLEdBQVdRLE1BQ3RDVyxFQUFvQmpELEVBQUU4QyxHQUFjSSxJQUFJRixHQUFrQkcsS0FFOURuRCxHQUFFQyxLQUFLZ0QsRUFBbUIsU0FBVS9DLEVBQUs2QixHQUN2Q1IscUJBQXFCTyxFQUFXQyxLQVVwQyxRQUFTZ0IsMEJBQXlCWCxHQUNoQyxHQUFJVSxLQUtKLE9BSkE5QyxHQUFFb0MsRUFBVyxXQUFXbkMsS0FBSyxXQUMzQjZDLEVBQWFKLEtBQUsxQyxFQUFFb0QsTUFBTWQsU0FHckJRLEVBbkpULEdBQUlPLFVBQTZCLGtCQUFYQyxTQUFvRCxnQkFBcEJBLFFBQU9DLFNBQXdCLFNBQVVDLEdBQU8sYUFBY0EsSUFBUyxTQUFVQSxHQUFPLE1BQU9BLElBQXlCLGtCQUFYRixTQUF5QkUsRUFBSUMsY0FBZ0JILFFBQVVFLElBQVFGLE9BQU9JLFVBQVksZUFBa0JGLElBRW5RaEQsUUFBNEIsa0JBQVg4QyxTQUF1RCxXQUE5QkQsU0FBU0MsT0FBT0MsVUFBeUIsU0FBVUMsR0FDL0YsV0FBc0IsS0FBUkEsRUFBc0IsWUFBY0gsU0FBU0csSUFDekQsU0FBVUEsR0FDWixNQUFPQSxJQUF5QixrQkFBWEYsU0FBeUJFLEVBQUlDLGNBQWdCSCxRQUFVRSxJQUFRRixPQUFPSSxVQUFZLGFBQTBCLEtBQVJGLEVBQXNCLFlBQWNILFNBQVNHLElBS3BLbkQiLCJmaWxlIjoic2l0ZS9wYXJ0aWFsL211bHRpcGxlLWxvY2F0aW9ucy5qcyIsInNvdXJjZXNDb250ZW50IjpbInZhciBfdHlwZW9mID0gdHlwZW9mIFN5bWJvbCA9PT0gXCJmdW5jdGlvblwiICYmIHR5cGVvZiBTeW1ib2wuaXRlcmF0b3IgPT09IFwic3ltYm9sXCIgPyBmdW5jdGlvbiAob2JqKSB7IHJldHVybiB0eXBlb2Ygb2JqOyB9IDogZnVuY3Rpb24gKG9iaikgeyByZXR1cm4gb2JqICYmIHR5cGVvZiBTeW1ib2wgPT09IFwiZnVuY3Rpb25cIiAmJiBvYmouY29uc3RydWN0b3IgPT09IFN5bWJvbCAmJiBvYmogIT09IFN5bWJvbC5wcm90b3R5cGUgPyBcInN5bWJvbFwiIDogdHlwZW9mIG9iajsgfTtcclxuXHJcbi8qIGdsb2JhbCBsb2FkSWQsIHVubG9hZElkLCBvbGRMb2NhdGlvbnMgKi9cclxuXHJcbnZhciBhcmNoaXZlZExvY2F0aW9ucyA9IHt9O1xyXG5cclxuLyoqXHJcbiAqIEFyY2hpdmVzIGxvY2F0aW9ucyBvcHRpb25zXHJcbiAqXHJcbiAqIEBwYXJhbSB7b2JqZWN0fSBsb2NhdGlvbnMgTGlzdCBvZiBsb2NhdGlvbnMgb2JqZWN0c1xyXG4gKi9cclxuZnVuY3Rpb24gYXJjaGl2ZUxvY2F0aW9ucyhsb2NhdGlvbnMpIHtcclxuICAkLmVhY2gobG9jYXRpb25zLCBmdW5jdGlvbiAoa2V5LCBsb2NhdGlvbikge1xyXG4gICAgaWYgKCEobG9jYXRpb24uaWQgaW4gYXJjaGl2ZWRMb2NhdGlvbnMpKSB7XHJcbiAgICAgIGFyY2hpdmVkTG9jYXRpb25zW2xvY2F0aW9uLmlkXSA9IGxvY2F0aW9uO1xyXG4gICAgfVxyXG4gIH0pO1xyXG59XHJcblxyXG4vKipcclxuICogQ2hlY2tzIHdoZXRoZXIgbG9jYXRpb24gaXMgYSBkaXJlY3Rpb25cclxuICpcclxuICogQHBhcmFtIHtvYmplY3R9IGRhdGEgSW5mb3JtYXRpb24gYWJvdXQgdGhlIGxvY2F0aW9uXHJcbiAqIEByZXR1cm5zIHtib29sZWFufVxyXG4gKi9cclxuZnVuY3Rpb24gaXNEaXJlY3Rpb24oZGF0YSkge1xyXG4gIHJldHVybiBfdHlwZW9mKGRhdGEuZGlyZWN0aW9uSWQpICE9PSAodHlwZW9mIHVuZGVmaW5lZCA9PT0gXCJ1bmRlZmluZWRcIiA/IFwidW5kZWZpbmVkXCIgOiBfdHlwZW9mKHVuZGVmaW5lZCkpICYmIGRhdGEuZGlyZWN0aW9uSWQgIT09IGZhbHNlO1xyXG59XHJcblxyXG4vKipcclxuICogUmV0dXJucyBkaXJlY3Rpb24gc3VnZ2VzdGlvbiBvcHRpb25cclxuICpcclxuICogQHBhcmFtIHtzdHJpbmd9IG5hbWUgTG9jYXRpb24gbmFtZVxyXG4gKiBAcmV0dXJucyB7c3RyaW5nfVxyXG4gKi9cclxuZnVuY3Rpb24gZ2V0RGlyZWN0aW9uU3VnZ2VzdGlvbihuYW1lKSB7XHJcbiAgcmV0dXJuIFwiPGRpdiBjbGFzcz0nZGlyZWN0aW9uJz5cIiArIG5hbWUgKyBcIjwvZGl2PlwiO1xyXG59XHJcblxyXG4vKipcclxuICogUmV0dXJucyBzaW1wbGUgc3VnZ2VzdGlvbiBvcHRpb25cclxuICpcclxuICogQHBhcmFtIHtvYmplY3R9IGRhdGEgSW5mb3JtYXRpb24gYWJvdXQgdGhlIGxvY2F0aW9uXHJcbiAqIEByZXR1cm5zIHtzdHJpbmd9XHJcbiAqL1xyXG5mdW5jdGlvbiBnZXRTaW1wbGVTdWdnZXN0aW9uKGRhdGEpIHtcclxuICByZXR1cm4gXCI8ZGl2PlwiICsgZGF0YS5uYW1lICsgXCI8ZGl2IGNsYXNzPSdwdWxsLXJpZ2h0Jz5cIiArIFwiPHNwYW4gY2xhc3M9J21hcC10ZXh0J1wiICsgXCJkYXRhLWxhdD0nXCIgKyBkYXRhLmxvY2F0aW9uLmxhdCArIFwiJyBcIiArIFwiZGF0YS1sb249J1wiICsgZGF0YS5sb2NhdGlvbi5sb24gKyBcIicgXCIgKyBcImRhdGEtem9vbT0nXCIgKyBkYXRhLnpvb20gKyBcIicgXCIgKyBcImRhdGEtdG9nZ2xlPSdwb3BvdmVyJyBcIiArIFwiZGF0YS1wbGFjZW1lbnQ9J3RvcCcgXCIgKyBcImRhdGEtY29udGVudD0nPGRpdiBjbGFzcz1cXFwibG9hZC1jaXR5LW1hcFxcXCI+PC9kaXY+Jz4gXCIgKyB0cmFuc2xhdGVNYXAgKyBcIjwvc3Bhbj5cIiArIFwiPC9kaXY+XCIgKyBcIjwvZGl2PlwiO1xyXG59XHJcblxyXG4vKipcclxuICogUmV0dXJucyBsb2NhdGlvbiBuYW1lXHJcbiAqXHJcbiAqIEBwYXJhbSB7b2JqZWN0fSBjdXJyZW50TG9jYXRpb24gQ3VycmVudGx5IHNlbGVjdGVkIGxvY2F0aW9uIG9iamVjdFxyXG4gKiBAcmV0dXJucyB7c3RyaW5nfVxyXG4gKi9cclxuZnVuY3Rpb24gZ2V0TG9jYXRpb25OYW1lKGN1cnJlbnRMb2NhdGlvbikge1xyXG4gIGlmICgkKGN1cnJlbnRMb2NhdGlvbikuYXR0cigndGV4dCcpICE9ICcnKSB7XHJcbiAgICByZXR1cm4gY3VycmVudExvY2F0aW9uLnRleHQ7XHJcbiAgfVxyXG5cclxuICB2YXIgbmFtZSA9ICcnO1xyXG4gICQuZWFjaChhcmNoaXZlZExvY2F0aW9ucywgZnVuY3Rpb24gKGFyY2hpdmVkTG9jYXRpb25JZCwgYXJjaGl2ZWRMb2NhdGlvbikge1xyXG4gICAgaWYgKGN1cnJlbnRMb2NhdGlvbi5pZCA9PSBhcmNoaXZlZExvY2F0aW9uSWQpIHtcclxuICAgICAgaWYgKGlzRGlyZWN0aW9uKGFyY2hpdmVkTG9jYXRpb24pKSB7XHJcbiAgICAgICAgcmVtb3ZlTG9jYXRpb25PcHRpb24obG9hZElkLCBhcmNoaXZlZExvY2F0aW9uSWQpO1xyXG4gICAgICAgIHVwZGF0ZUxvY2F0aW9uKGxvYWRJZCwgYXJjaGl2ZWRMb2NhdGlvbi5wb3B1bGFySWQsIGFyY2hpdmVkTG9jYXRpb24ucG9wdWxhck5hbWUpO1xyXG4gICAgICAgIHVwZGF0ZUxvY2F0aW9uKHVubG9hZElkLCBhcmNoaXZlZExvY2F0aW9uLmRpcmVjdGlvbklkLCBhcmNoaXZlZExvY2F0aW9uLmRpcmVjdGlvbk5hbWUpO1xyXG4gICAgICB9IGVsc2Uge1xyXG4gICAgICAgIG5hbWUgPSBhcmNoaXZlZExvY2F0aW9uLm5hbWU7XHJcbiAgICAgIH1cclxuICAgIH1cclxuICB9KTtcclxuICByZXR1cm4gbmFtZTtcclxufVxyXG5cclxuLyoqXHJcbiAqIFJlbW92ZXMgbG9jYXRpb24gb3B0aW9uIGZyb20gc2VsZWN0IGVsZW1lbnRcclxuICpcclxuICogQHBhcmFtIHtzdHJpbmd9IGVsZW1lbnRJZCBTZWxlY3QgZWxlbWVudCBJRFxyXG4gKiBAcGFyYW0ge3N0cmluZ30gbG9jYXRpb25JZCBTZWxlY3QgZWxlbWVudCBvcHRpb24gdmFsdWVcclxuICovXHJcbmZ1bmN0aW9uIHJlbW92ZUxvY2F0aW9uT3B0aW9uKGVsZW1lbnRJZCwgbG9jYXRpb25JZCkge1xyXG4gICQoJyMnICsgZWxlbWVudElkKS5maW5kKCdvcHRpb25bdmFsdWU9XCInICsgbG9jYXRpb25JZCArICdcIl0nKS5yZW1vdmUoKTtcclxufVxyXG5cclxuLyoqXHJcbiAqIFVwZGF0ZXMgbG9jYXRpb24gaW5mb3JtYXRpb24gaW4gc2VsZWN0IGVsZW1lbnRcclxuICpcclxuICogQHBhcmFtIHtzdHJpbmd9IGVsZW1lbnRJZCBTZWxlY3QgZWxlbWVudCBJRFxyXG4gKiBAcGFyYW0ge3N0cmluZ30gbG9jYXRpb25JZCBTZWxlY3QgZWxlbWVudCBvcHRpb24gdmFsdWVcclxuICogQHBhcmFtIHtzdHJpbmd9IGxvY2F0aW9uTmFtZSBMb2NhdGlvbiBuYW1lXHJcbiAqL1xyXG5mdW5jdGlvbiB1cGRhdGVMb2NhdGlvbihlbGVtZW50SWQsIGxvY2F0aW9uSWQsIGxvY2F0aW9uTmFtZSkge1xyXG4gIGlmICghaXNMb2NhdGlvbkV4aXN0cyhlbGVtZW50SWQsIGxvY2F0aW9uSWQpKSB7XHJcbiAgICB2YXIgc2VsZWN0b3IgPSAnIycgKyBlbGVtZW50SWQ7XHJcbiAgICB2YXIgc2VsZWN0ZWRMb2NhdGlvbnMgPSAkKHNlbGVjdG9yKS52YWwoKSA9PT0gbnVsbCA/IFtdIDogJChzZWxlY3RvcikudmFsKCk7XHJcbiAgICB2YXIgb3B0aW9uID0gJCgnPG9wdGlvbj48L29wdGlvbj4nKS5hdHRyKCd2YWx1ZScsIGxvY2F0aW9uSWQpLmh0bWwobG9jYXRpb25OYW1lKTtcclxuICAgICQoc2VsZWN0b3IpLmFwcGVuZChvcHRpb24pO1xyXG4gICAgc2VsZWN0ZWRMb2NhdGlvbnMucHVzaChsb2NhdGlvbklkKTtcclxuICAgICQoc2VsZWN0b3IpLnZhbChzZWxlY3RlZExvY2F0aW9ucykuY2hhbmdlKCk7XHJcbiAgfVxyXG59XHJcblxyXG4vKipcclxuICogQ2hlY2tzIHdoZXRoZXIgbG9jYXRpb24gaXMgYWxyZWFkeSBpbmNsdWRlZCBpbiBzZWxlY3RvclxyXG4gKlxyXG4gKiBAcGFyYW0ge3N0cmluZ30gZWxlbWVudElkIFNlbGVjdCBlbGVtZW50IElEXHJcbiAqIEBwYXJhbSB7c3RyaW5nfSBsb2NhdGlvbklkIFNlbGVjdCBlbGVtZW50IG9wdGlvbiB2YWx1ZVxyXG4gKiBAcmV0dXJucyB7Ym9vbGVhbn1cclxuICovXHJcbmZ1bmN0aW9uIGlzTG9jYXRpb25FeGlzdHMoZWxlbWVudElkLCBsb2NhdGlvbklkKSB7XHJcbiAgcmV0dXJuICQoJyMnICsgZWxlbWVudElkKS5maW5kKCdvcHRpb25bdmFsdWU9XCInICsgbG9jYXRpb25JZCArICdcIl0nKS5sZW5ndGggPiAwO1xyXG59XHJcblxyXG4vKipcclxuICogUmVtb3ZlcyB1bnNlbGVjdGVkIGxvY2F0aW9uc1xyXG4gKlxyXG4gKiBAcGFyYW0ge3N0cmluZ30gZWxlbWVudElkIFNlbGVjdCBlbGVtZW50IElEXHJcbiAqL1xyXG5mdW5jdGlvbiByZW1vdmVMb2NhdGlvbihlbGVtZW50SWQpIHtcclxuICB2YXIgb2xkTG9jYXRpb25zID0gZ2V0TG9jYXRpb25zQmVmb3JlUmVtb3ZlKCcjJyArIGVsZW1lbnRJZCk7XHJcbiAgdmFyIGN1cnJlbnRMb2NhdGlvbnMgPSAkKCcjJyArIGVsZW1lbnRJZCkudmFsKCk7XHJcbiAgdmFyIGxvY2F0aW9uc1RvUmVtb3ZlID0gJChvbGRMb2NhdGlvbnMpLm5vdChjdXJyZW50TG9jYXRpb25zKS5nZXQoKTtcclxuXHJcbiAgJC5lYWNoKGxvY2F0aW9uc1RvUmVtb3ZlLCBmdW5jdGlvbiAoa2V5LCBsb2NhdGlvbklkKSB7XHJcbiAgICByZW1vdmVMb2NhdGlvbk9wdGlvbihlbGVtZW50SWQsIGxvY2F0aW9uSWQpO1xyXG4gIH0pO1xyXG59XHJcblxyXG4vKipcclxuICogUmV0dXJucyBsb2NhdGlvbnMgSURzIGJlZm9yZSByZW1vdmUgZXZlbnRcclxuICpcclxuICogQHBhcmFtIHtzdHJpbmd9IHNlbGVjdG9yIFNlbGVjdCBlbGVtZW50IElEXHJcbiAqIEByZXR1cm5zIHtBcnJheX1cclxuICovXHJcbmZ1bmN0aW9uIGdldExvY2F0aW9uc0JlZm9yZVJlbW92ZShzZWxlY3Rvcikge1xyXG4gIHZhciBvbGRMb2NhdGlvbnMgPSBbXTtcclxuICAkKHNlbGVjdG9yICsgJyBvcHRpb24nKS5lYWNoKGZ1bmN0aW9uICgpIHtcclxuICAgIG9sZExvY2F0aW9ucy5wdXNoKCQodGhpcykudmFsKCkpO1xyXG4gIH0pO1xyXG5cclxuICByZXR1cm4gb2xkTG9jYXRpb25zO1xyXG59XHJcbiJdfQ==
