const monthYearElement = document.getElementById('monthYear');
const datesElement = document.getElementById('dates');
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');

let currentDate = new Date();

const updateCalendar = () => {
    // Faça uma solicitação AJAX para obter os eventos agendados para o mês atual
    fetch('./php/eventos-calendario.php')
        .then(response => response.json())
        .then(data => {
            // Adicione uma classe 'has-event' aos dias com eventos
            data.forEach(event => {
                const eventDate = new Date(event.data);
                const dayElement = datesElement.querySelector(`.date[data-date="${eventDate.toISOString().split('T')[0]}"]`);
                if (dayElement) {
                    dayElement.classList.add('has-event');
                }
            });
        })
        .catch(error => {
            console.error('Erro ao obter os eventos:', error);
        });

    const currentYear = currentDate.getFullYear();
    const currentMonth = currentDate.getMonth();

    const firstDay = new Date(currentYear, currentMonth, 1); // Corrigido para começar no dia 1
    const lastDay = new Date(currentYear, currentMonth + 1, 0);
    const totalDays = lastDay.getDate();
    const firstDayIndex = firstDay.getDay();
    const lastDayIndex = lastDay.getDay();

    const monthName = currentDate.toLocaleString('default', { month: 'long' });
    const monthYearString = `${monthName.charAt(0).toUpperCase()}${monthName.slice(1)} ${currentYear}`;
    monthYearElement.textContent = monthYearString;

    let datesHTML = '';

    for (let i = firstDayIndex; i > 0; i--) {
        const prevDate = new Date(currentYear, currentMonth, 0 - i + 1);
        datesHTML += `<div class="date inactive">${prevDate.getDate()}</div>`;
    }

    for (let i = 1; i <= totalDays; i++) {
        const date = new Date(currentYear, currentMonth, i);
        const dateString = date.toISOString().split('T')[0]; // Adicionado atributo data-date
        const activeClass = date.toDateString() === new Date().toDateString() ? 'active' : '';
        datesHTML += `<div class="date ${activeClass}" data-date="${dateString}">${i}</div>`; // Adicionado atributo data-date
    }

    for (let i = 1; i <= 7 - lastDayIndex; i++) {
        const nextDate = new Date(currentYear, currentMonth + 1, i);
        datesHTML += `<div class="date inactive">${nextDate.getDate()}</div>`;
    }

    datesElement.innerHTML = datesHTML;
}

prevBtn.addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() - 1);
    updateCalendar();
})

nextBtn.addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() + 1);
    updateCalendar();
})

updateCalendar();
