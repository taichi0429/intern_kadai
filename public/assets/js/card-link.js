// data-href を持つ.cardクリックでページ遷移する（内部のリンク・ボタン・フォームのクリックは対象外）
document.addEventListener('click', function (event) {
  var card = event.target.closest('.card[data-href]');

  if (!card) {
    return;
  }

  if (event.target.closest('a, button, form')) {
    return;
  }

  window.location.href = card.getAttribute('data-href');
});
