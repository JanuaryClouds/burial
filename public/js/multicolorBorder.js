export default function randomizeMulticolorBorder() {
    document.querySelectorAll('.card.multicolor-border').forEach(card => {
        const redEnd = Math.floor(Math.random() * 40) + 20;
        const yellowEnd = Math.floor(Math.random() * (90 - redEnd)) + redEnd;

        card.style.setProperty('--red-end', `${redEnd}%`);
        card.style.setProperty('--yellow-end', `${yellowEnd}%`);
    });
}
