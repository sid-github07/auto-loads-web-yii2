$(document).ready(function () {
  let seoNav = $('#seo-nav')
  let selected = sessionStorage.getItem('tab.selected')
  let a = seoNav.find('a[href=' + selected + ']')
  if (a.length) {
    a.click()
  }
  seoNav.on('click', 'a[data-toggle=tab]', function (v) {
    sessionStorage.setItem('tab.selected', v.currentTarget.hash)
  })
})