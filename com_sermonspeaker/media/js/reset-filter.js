/**
 * @package     SermonSpeaker
 * @subpackage  Component.Media
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2021 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/
(document => {

  const onClick = () => {
    const form = document.getElementById('adminForm');
    if(document.getElementById('filter-search')){
      document.getElementById('filter-search').value = '';
    }
    if(document.getElementById('filter-tag')){
      document.getElementById('filter-tag').value = '';
    }
    if(document.getElementById('filter-book')){
      document.getElementById('filter-book').value = '';
    }
    if(document.getElementById('filter-month')){
      document.getElementById('filter-month').value = '';
    }
    if(document.getElementById('filter-year')){
      document.getElementById('filter-year').value = '';
    }
    if(document.getElementById('filter_order')){
      document.getElementById('filter_order').value = '';
    }
    if(document.getElementById('filter_order_Dir')){
      document.getElementById('filter_order_Dir').value = '';
    }
    form.submit();
  };

  const onBoot = () => {
    const form = document.getElementById('adminForm');
    const element = form.querySelector('button[type="reset"]');

    if (element) {
      element.addEventListener('click', onClick);
    }

    document.removeEventListener('DOMContentLoaded', onBoot);
  };

  document.addEventListener('DOMContentLoaded', onBoot);
})(document);
