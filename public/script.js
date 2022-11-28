{
    // ハンバーガーメニュー
    const h = document.getElementById("hamburger");
    h.addEventListener("click", () => {
        h.classList.toggle("on");
    });

    // ツイート取得の場合の期間指定の表示
    const objects = document.getElementsByName("object");
    const tweets = document.getElementById("tweets");
    const u = document.getElementById("using_term_area");
    for (let i = 0; i < objects.length; i++) {
        objects[i].addEventListener('change', () => {
            if (tweets.checked) {
                u.style.display = "block";
            } else {
                // u.style.display = "none";
            }
        });
    }
    window.onload = () => {
        if (tweets.checked) {
            u.style.display = "block";
        } else {
            // u.style.display = "none";
        }
    }
}
