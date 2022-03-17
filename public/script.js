{
    // ハンバーガーメニュー
	const h = document.getElementById('hamburger');
    h.addEventListener('click', () => {
        h.classList.toggle('on');
    });
}