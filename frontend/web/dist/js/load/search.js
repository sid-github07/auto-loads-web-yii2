"use strict";function registerLoadDirectionChange(){$("#IK-C-10").unbind("change.direction").bind("change.direction",function(){loadDirectionChange()})}function loadDirectionChange(){var t,n,i=$("#IK-C-10").val();if(isMultipleCities(i))$.each(i,function(i,e){if(isDirection(e)){var o=splitDirection(e),r=_slicedToArray(o,2);t=r[0],n=r[1],updateCities(e,t,"load"),updateCities(e,n,"unload")}});else{if(!isDirection(i))return;var e=splitDirection(i),o=_slicedToArray(e,2);t=o[0],n=o[1],updateCity(t,"load"),updateCity(n,"unload")}}function isMultipleCities(t){return"object"==(void 0===t?"undefined":_typeof(t))}function updateCities(t,n,i){var e="load"===i?"#IK-C-10":"#IK-C-11",o=$(e).val(),r=null===o?[]:o;removeDirectionOption(e,t),appendCities(e,n),supplementCities(e,n,r)}function removeDirectionOption(t,n){$(t).find('option[value="'+n+'"]').remove()}function appendCities(t,n){0==$(t).find('option[value="'+n+'"]').length&&$(t).append($("<option></option>").attr("value",n))}function supplementCities(t,n,i){i.push(n),$(t).val(i).change()}function updateCity(t,n){var i="load"===n?"#IK-C-10":"#IK-C-11";$(i).append($("<option></option>").attr("value",t)).val(t).change()}function isDirection(t){return t.indexOf("-")>=0}function splitDirection(t){return[t.split("-")[0],t.split("-")[1]]}var _typeof2="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},_typeof="function"==typeof Symbol&&"symbol"===_typeof2(Symbol.iterator)?function(t){return void 0===t?"undefined":_typeof2(t)}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":void 0===t?"undefined":_typeof2(t)},_slicedToArray=function(){function t(t,n){var i=[],e=!0,o=!1,r=void 0;try{for(var u,a=t[Symbol.iterator]();!(e=(u=a.next()).done)&&(i.push(u.value),!n||i.length!==n);e=!0);}catch(t){o=!0,r=t}finally{try{!e&&a.return&&a.return()}finally{if(o)throw r}}return i}return function(n,i){if(Array.isArray(n))return n;if(Symbol.iterator in Object(n))return t(n,i);throw new TypeError("Invalid attempt to destructure non-iterable instance")}}();$(document).ready(function(){registerLoadDirectionChange()});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImxvYWQvc2VhcmNoLmpzIl0sIm5hbWVzIjpbInJlZ2lzdGVyTG9hZERpcmVjdGlvbkNoYW5nZSIsIiQiLCJ1bmJpbmQiLCJiaW5kIiwibG9hZERpcmVjdGlvbkNoYW5nZSIsImxvYWQiLCJ1bmxvYWQiLCJjaXRpZXMiLCJ2YWwiLCJpc011bHRpcGxlQ2l0aWVzIiwiZWFjaCIsImtleSIsImNpdHkiLCJpc0RpcmVjdGlvbiIsIl9zcGxpdERpcmVjdGlvbiIsInNwbGl0RGlyZWN0aW9uIiwiX3NwbGl0RGlyZWN0aW9uMiIsIl9zbGljZWRUb0FycmF5IiwidXBkYXRlQ2l0aWVzIiwiX3NwbGl0RGlyZWN0aW9uMyIsIl9zcGxpdERpcmVjdGlvbjQiLCJ1cGRhdGVDaXR5IiwiX3R5cGVvZiIsImRpcmVjdGlvbiIsInR5cGUiLCJzZWxlY3RvciIsInZhbHVlIiwicmVtb3ZlRGlyZWN0aW9uT3B0aW9uIiwiYXBwZW5kQ2l0aWVzIiwic3VwcGxlbWVudENpdGllcyIsImZpbmQiLCJyZW1vdmUiLCJsZW5ndGgiLCJhcHBlbmQiLCJhdHRyIiwicHVzaCIsImNoYW5nZSIsImluZGV4T2YiLCJzcGxpdCIsIl90eXBlb2YyIiwiU3ltYm9sIiwiaXRlcmF0b3IiLCJvYmoiLCJjb25zdHJ1Y3RvciIsInByb3RvdHlwZSIsInNsaWNlSXRlcmF0b3IiLCJhcnIiLCJpIiwiX2FyciIsIl9uIiwiX2QiLCJfZSIsInVuZGVmaW5lZCIsIl9zIiwiX2kiLCJuZXh0IiwiZG9uZSIsImVyciIsIkFycmF5IiwiaXNBcnJheSIsIk9iamVjdCIsIlR5cGVFcnJvciIsImRvY3VtZW50IiwicmVhZHkiXSwibWFwcGluZ3MiOiJBQUFBLFlBOENBLFNBQVNBLCtCQUNMQyxFQUFFLFlBQVlDLE9BQU8sb0JBQW9CQyxLQUFLLG1CQUFvQixXQUM5REMsd0JBT1IsUUFBU0EsdUJBRUwsR0FDSUMsR0FDQUMsRUFGQUMsRUFBU04sRUFBRSxZQUFZTyxLQUczQixJQUFJQyxpQkFBaUJGLEdBQ2pCTixFQUFF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RSxHQUFJSixFQUFJLEtBQU1DLElBRXJCLE1BQU9ILEdBQ1gsTUFBTyxVQUFVRixFQUFLQyxHQUNuQixHQUFJVyxNQUFNQyxRQUFRYixHQUNkLE1BQU9BLEVBQ0osSUFBSU4sT0FBT0MsV0FBWW1CLFFBQU9kLEdBQ2pDLE1BQU9ELEdBQWNDLEVBQUtDLEVBRTFCLE1BQU0sSUFBSWMsV0FBVSwyREFRaEM1RCxHQUFFNkQsVUFBVUMsTUFBTSxXQUNkL0QiLCJmaWxlIjoibG9hZC9zZWFyY2guanMiLCJzb3VyY2VzQ29udGVudCI6WyIndXNlIHN0cmljdCc7XHJcblxyXG52YXIgX3R5cGVvZiA9IHR5cGVvZiBTeW1ib2wgPT09IFwiZnVuY3Rpb25cIiAmJiB0eXBlb2YgU3ltYm9sLml0ZXJhdG9yID09PSBcInN5bWJvbFwiID8gZnVuY3Rpb24gKG9iaikgeyByZXR1cm4gdHlwZW9mIG9iajsgfSA6IGZ1bmN0aW9uIChvYmopIHsgcmV0dXJuIG9iaiAmJiB0eXBlb2YgU3ltYm9sID09PSBcImZ1bmN0aW9uXCIgJiYgb2JqLmNvbnN0cnVjdG9yID09PSBTeW1ib2wgJiYgb2JqICE9PSBTeW1ib2wucHJvdG90eXBlID8gXCJzeW1ib2xcIiA6IHR5cGVvZiBvYmo7IH07XHJcblxyXG52YXIgX3NsaWNlZFRvQXJyYXkgPSBmdW5jdGlvbiAoKSB7IGZ1bmN0aW9uIHNsaWNlSXRlcmF0b3IoYXJyLCBpKSB7IHZhciBfYXJyID0gW107IHZhciBfbiA9IHRydWU7IHZhciBfZCA9IGZhbHNlOyB2YXIgX2UgPSB1bmRlZmluZWQ7IHRyeSB7IGZvciAodmFyIF9pID0gYXJyW1N5bWJvbC5pdGVyYXRvcl0oKSwgX3M7ICEoX24gPSAoX3MgPSBfaS5uZXh0KCkpLmRvbmUpOyBfbiA9IHRydWUpIHsgX2Fyci5wdXNoKF9zLnZhbHVlKTsgaWYgKGkgJiYgX2Fyci5sZW5ndGggPT09IGkpIGJyZWFrOyB9IH0gY2F0Y2ggKGVycikgeyBfZCA9IHRydWU7IF9lID0gZXJyOyB9IGZpbmFsbHkgeyB0cnkgeyBpZiAoIV9uICYmIF9pW1wicmV0dXJuXCJdKSBfaVtcInJldHVyblwiXSgpOyB9IGZpbmFsbHkgeyBpZiAoX2QpIHRocm93IF9lOyB9IH0gcmV0dXJuIF9hcnI7IH0gcmV0dXJuIGZ1bmN0aW9uIChhcnIsIGkpIHsgaWYgKEFycmF5LmlzQXJyYXkoYXJyKSkgeyByZXR1cm4gYXJyOyB9IGVsc2UgaWYgKFN5bWJvbC5pdGVyYXRvciBpbiBPYmplY3QoYXJyKSkgeyByZXR1cm4gc2xpY2VJdGVyYXRvcihhcnIsIGkpOyB9IGVsc2UgeyB0aHJvdyBuZXcgVHlwZUVycm9yKFwiSW52YWxpZCBhdHRlbXB0IHRvIGRlc3RydWN0dXJlIG5vbi1pdGVyYWJsZSBpbnN0YW5jZVwiKTsgfSB9OyB9KCk7XHJcblxyXG4vKipcclxuICogRXhlY3V0ZXMgYW5vbnltb3VzIGZ1bmN0aW9uIHdoZW4gZG9jdW1lbnQgaXMgcmVhZHlcclxuICovXHJcbiQoZG9jdW1lbnQpLnJlYWR5KGZ1bmN0aW9uICgpIHtcclxuICAgIHJlZ2lzdGVyTG9hZERpcmVjdGlvbkNoYW5nZSgpO1xyXG59KTtcclxuXHJcbi8qKlxyXG4gKiBSZWdpc3RlcnMgbG9hZCBkaXJlY3Rpb24gY2hhbmdlIGV2ZW50XHJcbiAqL1xyXG5mdW5jdGlvbiByZWdpc3RlckxvYWREaXJlY3Rpb25DaGFuZ2UoKSB7XHJcbiAgICAkKCcjSUstQy0xMCcpLnVuYmluZCgnY2hhbmdlLmRpcmVjdGlvbicpLmJpbmQoJ2NoYW5nZS5kaXJlY3Rpb24nLCBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgbG9hZERpcmVjdGlvbkNoYW5nZSgpO1xyXG4gICAgfSk7XHJcbn1cclxuXHJcbi8qKlxyXG4gKiBVcGRhdGVzIGxvYWQgYW5kIHVubG9hZCBjaXRpZXMgd2hlbiB1c2VyIHNlbGVjdHMgY2l0eSB3aXRoIGRpcmVjdGlvblxyXG4gKi9cclxuZnVuY3Rpb24gbG9hZERpcmVjdGlvbkNoYW5nZSgpIHtcclxuICAgIC8qKiBAbWVtYmVyIHtvYmplY3R8c3RyaW5nfSBPYmplY3Qgb2YgY2l0aWVzIElEcyBvciBDaXR5IElEIHN0cmluZyAqL1xyXG4gICAgdmFyIGNpdGllcyA9ICQoJyNJSy1DLTEwJykudmFsKCk7XHJcbiAgICB2YXIgbG9hZDtcclxuICAgIHZhciB1bmxvYWQ7XHJcbiAgICBpZiAoaXNNdWx0aXBsZUNpdGllcyhjaXRpZXMpKSB7XHJcbiAgICAgICAgJC5lYWNoKGNpdGllcywgZnVuY3Rpb24gKGtleSwgY2l0eSkge1xyXG4gICAgICAgICAgICBpZiAoIWlzRGlyZWN0aW9uKGNpdHkpKSB7XHJcbiAgICAgICAgICAgICAgICByZXR1cm47XHJcbiAgICAgICAgICAgIH1cclxuXHJcbiAgICAgICAgICAgIHZhciBfc3BsaXREaXJlY3Rpb24gPSBzcGxpdERpcmVjdGlvbihjaXR5KTtcclxuXHJcbiAgICAgICAgICAgIHZhciBfc3BsaXREaXJlY3Rpb24yID0gX3NsaWNlZFRvQXJyYXkoX3NwbGl0RGlyZWN0aW9uLCAyKTtcclxuXHJcbiAgICAgICAgICAgIGxvYWQgPSBfc3BsaXREaXJlY3Rpb24yWzBdO1xyXG4gICAgICAgICAgICB1bmxvYWQgPSBfc3BsaXREaXJlY3Rpb24yWzFdO1xyXG5cclxuICAgICAgICAgICAgdXBkYXRlQ2l0aWVzKGNpdHksIGxvYWQsICdsb2FkJyk7XHJcbiAgICAgICAgICAgIHVwZGF0ZUNpdGllcyhjaXR5LCB1bmxvYWQsICd1bmxvYWQnKTtcclxuICAgICAgICB9KTtcclxuICAgIH0gZWxzZSB7XHJcbiAgICAgICAgaWYgKCFpc0RpcmVjdGlvbihjaXRpZXMpKSB7XHJcbiAgICAgICAgICAgIHJldHVybjtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIHZhciBfc3BsaXREaXJlY3Rpb24zID0gc3BsaXREaXJlY3Rpb24oY2l0aWVzKTtcclxuXHJcbiAgICAgICAgdmFyIF9zcGxpdERpcmVjdGlvbjQgPSBfc2xpY2VkVG9BcnJheShfc3BsaXREaXJlY3Rpb24zLCAyKTtcclxuXHJcbiAgICAgICAgbG9hZCA9IF9zcGxpdERpcmVjdGlvbjRbMF07XHJcbiAgICAgICAgdW5sb2FkID0gX3NwbGl0RGlyZWN0aW9uNFsxXTtcclxuXHJcbiAgICAgICAgdXBkYXRlQ2l0eShsb2FkLCAnbG9hZCcpO1xyXG4gICAgICAgIHVwZGF0ZUNpdHkodW5sb2FkLCAndW5sb2FkJyk7XHJcbiAgICB9XHJcbn1cclxuXHJcbi8qKlxyXG4gKiBDaGVja3Mgd2hldGhlciBzZWFyY2hpbmcgbXVsdGlwbGUgY2l0aWVzXHJcbiAqXHJcbiAqIEBwYXJhbSB7b2JqZWN0fHN0cmluZ30gY2l0aWVzIE9iamVjdCBvZiBjaXRpZXMgSURzIG9yIENpdHkgSUQgc3RyaW5nXHJcbiAqIEByZXR1cm5zIHtib29sZWFufVxyXG4gKi9cclxuZnVuY3Rpb24gaXNNdWx0aXBsZUNpdGllcyhjaXRpZXMpIHtcclxuICAgIHJldHVybiAodHlwZW9mIGNpdGllcyA9PT0gJ3VuZGVmaW5lZCcgPyAndW5kZWZpbmVkJyA6IF90eXBlb2YoY2l0aWVzKSkgPT0gJ29iamVjdCc7XHJcbn1cclxuXHJcbi8qKlxyXG4gKiBVcGRhdGVzIGxvYWQvdW5sb2FkIGNpdGllc1xyXG4gKlxyXG4gKiBAcGFyYW0ge3N0cmluZ30gZGlyZWN0aW9uIERpcmVjdGlvbiBJRFxyXG4gKiBAcGFyYW0ge3N0cmluZ30gY2l0eSBDaXR5IElEXHJcbiAqIEBwYXJhbSB7c3RyaW5nfSB0eXBlIFdvcmQ6ICdsb2FkJyBvciAndW5sb2FkJ1xyXG4gKi9cclxuZnVuY3Rpb24gdXBkYXRlQ2l0aWVzKGRpcmVjdGlvbiwgY2l0eSwgdHlwZSkge1xyXG4gICAgLyoqIEBtZW1iZXIge3N0cmluZ30gTG9hZC91bmxvYWQgaW5wdXQgSUQgKi9cclxuICAgIHZhciBzZWxlY3RvciA9IHR5cGUgPT09ICdsb2FkJyA/ICcjSUstQy0xMCcgOiAnI0lLLUMtMTEnO1xyXG4gICAgLyoqIEBtZW1iZXIge251bGx8b2JqZWN0fSBMb2FkL3VubG9hZCBjaXRpZXMgKi9cclxuICAgIHZhciB2YWx1ZSA9ICQoc2VsZWN0b3IpLnZhbCgpO1xyXG4gICAgLyoqIEBtZW1iZXIge29iamVjdH0gTG9hZC91bmxvYWQgY2l0aWVzICovXHJcbiAgICB2YXIgY2l0aWVzID0gdmFsdWUgPT09IG51bGwgPyBbXSA6IHZhbHVlO1xyXG5cclxuICAgIHJlbW92ZURpcmVjdGlvbk9wdGlvbihzZWxlY3RvciwgZGlyZWN0aW9uKTtcclxuICAgIGFwcGVuZENpdGllcyhzZWxlY3RvciwgY2l0eSk7XHJcbiAgICBzdXBwbGVtZW50Q2l0aWVzKHNlbGVjdG9yLCBjaXR5LCBjaXRpZXMpO1xyXG59XHJcblxyXG4vKipcclxuICogUmVtb3ZlcyBkaXJlY3Rpb24gb3B0aW9uIGZyb20gbG9hZC91bmxvYWQgaW5wdXRcclxuICpcclxuICogQHBhcmFtIHtzdHJpbmd9IHNlbGVjdG9yIExvYWQvdW5sb2FkIGlucHV0IElEXHJcbiAqIEBwYXJhbSB7c3RyaW5nfSBkaXJlY3Rpb24gRGlyZWN0aW9uIElEXHJcbiAqL1xyXG5mdW5jdGlvbiByZW1vdmVEaXJlY3Rpb25PcHRpb24oc2VsZWN0b3IsIGRpcmVjdGlvbikge1xyXG4gICAgJChzZWxlY3RvcikuZmluZCgnb3B0aW9uW3ZhbHVlPVwiJyArIGRpcmVjdGlvbiArICdcIl0nKS5yZW1vdmUoKTtcclxufVxyXG5cclxuLyoqXHJcbiAqIEFwcGVuZHMgY2l0eVxyXG4gKlxyXG4gKiBAcGFyYW0ge3N0cmluZ30gc2VsZWN0b3IgTG9hZC91bmxvYWQgaW5wdXQgSURcclxuICogQHBhcmFtIHtzdHJpbmd9IGNpdHkgQ2l0eSBJRFxyXG4gKi9cclxuZnVuY3Rpb24gYXBwZW5kQ2l0aWVzKHNlbGVjdG9yLCBjaXR5KSB7XHJcbiAgICBpZiAoJChzZWxlY3RvcikuZmluZCgnb3B0aW9uW3ZhbHVlPVwiJyArIGNpdHkgKyAnXCJdJykubGVuZ3RoID09IDApIHtcclxuICAgICAgICAkKHNlbGVjdG9yKS5hcHBlbmQoJCgnPG9wdGlvbj48L29wdGlvbj4nKS5hdHRyKCd2YWx1ZScsIGNpdHkpKTtcclxuICAgIH1cclxufVxyXG5cclxuLyoqXHJcbiAqIFN1cHBsZW1lbnRzIGNpdGllc1xyXG4gKlxyXG4gKiBAcGFyYW0ge3N0cmluZ30gc2VsZWN0b3IgTG9hZC91bmxvYWQgaW5wdXQgSURcclxuICogQHBhcmFtIHtzdHJpbmd9IGNpdHkgQ2l0eSBJRFxyXG4gKiBAcGFyYW0ge29iamVjdH0gY2l0aWVzIExpc3Qgb2YgbG9hZC91bmxvYWQgY2l0aWVzXHJcbiAqL1xyXG5mdW5jdGlvbiBzdXBwbGVtZW50Q2l0aWVzKHNlbGVjdG9yLCBjaXR5LCBjaXRpZXMpIHtcclxuICAgIGNpdGllcy5wdXNoKGNpdHkpO1xyXG4gICAgJChzZWxlY3RvcikudmFsKGNpdGllcykuY2hhbmdlKCk7XHJcbn1cclxuXHJcbi8qKlxyXG4gKiBVcGRhdGVzIGNpdHlcclxuICpcclxuICogQHBhcmFtIHtzdHJpbmd9IGNpdHkgQ2l0eSBJRFxyXG4gKiBAcGFyYW0ge3N0cmluZ30gdHlwZSBXb3JkOiAnbG9hZCcgb3IgJ3VubG9hZCdcclxuICovXHJcbmZ1bmN0aW9uIHVwZGF0ZUNpdHkoY2l0eSwgdHlwZSkge1xyXG4gICAgLyoqIEBtZW1iZXIge3N0cmluZ30gTG9hZCBvciB1bmxvYWQgY2l0eSBpbnB1dCBJRCAqL1xyXG4gICAgdmFyIHNlbGVjdG9yID0gdHlwZSA9PT0gJ2xvYWQnID8gJyNJSy1DLTEwJyA6ICcjSUstQy0xMSc7XHJcbiAgICAkKHNlbGVjdG9yKS5hcHBlbmQoJCgnPG9wdGlvbj48L29wdGlvbj4nKS5hdHRyKCd2YWx1ZScsIGNpdHkpKS52YWwoY2l0eSkuY2hhbmdlKCk7XHJcbn1cclxuXHJcbi8qKlxyXG4gKiBDaGVja3Mgd2hldGhlciBzZWxlY3RlZCBjaXR5IGlzIGNpdHkgd2l0aCBkaXJlY3Rpb25cclxuICpcclxuICogQHBhcmFtIHtzdHJpbmd9IGNpdHkgU2VsZWN0ZWQgY2l0eSBJRFxyXG4gKiBAcmV0dXJucyB7Ym9vbGVhbn1cclxuICovXHJcbmZ1bmN0aW9uIGlzRGlyZWN0aW9uKGNpdHkpIHtcclxuICAgIHJldHVybiBjaXR5LmluZGV4T2YoJy0nKSA+PSAwO1xyXG59XHJcblxyXG4vKipcclxuICogU3BsaXRzIGRpcmVjdGlvbiB0byBsb2FkIGFuZCB1bmxvYWQgY2l0aWVzXHJcbiAqXHJcbiAqIEBwYXJhbSB7c3RyaW5nfSBkaXJlY3Rpb25cclxuICogQHJldHVybnMge1t7c3RyaW5nfSx7c3RyaW5nfV19XHJcbiAqL1xyXG5mdW5jdGlvbiBzcGxpdERpcmVjdGlvbihkaXJlY3Rpb24pIHtcclxuICAgIC8qKiBAbWVtYmVyIHtzdHJpbmd9IExvYWQgY2l0eSBJRCAqL1xyXG4gICAgdmFyIGxvYWQgPSBkaXJlY3Rpb24uc3BsaXQoJy0nKVswXTtcclxuICAgIC8qKiBAbWVtYmVyIHtzdHJpbmd9IFVubG9hZCBjaXR5IElEICovXHJcbiAgICB2YXIgdW5sb2FkID0gZGlyZWN0aW9uLnNwbGl0KCctJylbMV07XHJcblxyXG4gICAgcmV0dXJuIFtsb2FkLCB1bmxvYWRdO1xyXG59Il19