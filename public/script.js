{
    // ハンバーガーメニュー
	const h = document.getElementById('hamburger');
    h.addEventListener('click', () => {
        h.classList.toggle('on');
    });

    // ツイート取得の場合の期間指定の表示
    const t = document.getElementById('tweets');
    const u = document.getElementById('using_term_area');
    console.log('test');
}