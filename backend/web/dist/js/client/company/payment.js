"use strict";function changePaymentYear(){var a=$(".payment-year").val();$.pjax({type:"POST",url:actionCompanyPayments,data:{year:a},container:"#company-payment-pjax",push:!1,scrollTo:!1,cache:!1})}
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImNsaWVudC9jb21wYW55L3BheW1lbnQuanMiXSwibmFtZXMiOlsiY2hhbmdlUGF5bWVudFllYXIiLCJ5ZWFyIiwiJCIsInZhbCIsInBqYXgiLCJ0eXBlIiwidXJsIiwiYWN0aW9uQ29tcGFueVBheW1lbnRzIiwiZGF0YSIsImNvbnRhaW5lciIsInB1c2giLCJzY3JvbGxUbyIsImNhY2hlIl0sIm1hcHBpbmdzIjoiQUFBQSxZQU9BLFNBQVNBLHFCQUNMLEdBQUlDLEdBQU9DLEVBQUUsaUJBQWlCQyxLQUM5QkQsR0FBRUUsTUFDRUMsS0FBTSxPQUNOQyxJQUFLQyxzQkFDTEMsTUFDSVAsS0FBTUEsR0FFVlEsVUFBVyx3QkFDWEMsTUFBTSxFQUNOQyxVQUFVLEVBQ1ZDLE9BQU8iLCJmaWxlIjoiY2xpZW50L2NvbXBhbnkvcGF5bWVudC5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIGdsb2JhbCBhY3Rpb25Db21wYW55UGF5bWVudHMgKi9cclxuXHJcbi8qKlxyXG4gKiBDaGFuZ2VzIGNvbXBhbnkgcGF5bWVudCB2aXNpYmxlIHllYXJcclxuICovXHJcbmZ1bmN0aW9uIGNoYW5nZVBheW1lbnRZZWFyKCkge1xyXG4gICAgdmFyIHllYXIgPSAkKCcucGF5bWVudC15ZWFyJykudmFsKCk7XHJcbiAgICAkLnBqYXgoe1xyXG4gICAgICAgIHR5cGU6ICdQT1NUJyxcclxuICAgICAgICB1cmw6IGFjdGlvbkNvbXBhbnlQYXltZW50cyxcclxuICAgICAgICBkYXRhOiB7XHJcbiAgICAgICAgICAgIHllYXI6IHllYXJcclxuICAgICAgICB9LFxyXG4gICAgICAgIGNvbnRhaW5lcjogJyNjb21wYW55LXBheW1lbnQtcGpheCcsXHJcbiAgICAgICAgcHVzaDogZmFsc2UsXHJcbiAgICAgICAgc2Nyb2xsVG86IGZhbHNlLFxyXG4gICAgICAgIGNhY2hlOiBmYWxzZVxyXG4gICAgfSk7XHJcbn0iXX0=