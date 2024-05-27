const monthYearElement = document.getElementById('monthYear');
const datesElement = document.getElementById('dates');
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');

let currentDate = new Date();
let eventos = []; // Array para armazenar os eventos

const formatDate = (dateString) => {
    const [year, month, day] = dateString.split('-');
    return `${day}/${month}/${year}`;
};

const formatTime = (timeString) => {
    const [hour, minute] = timeString.split(':');
    return `${hour}:${minute}`;
};

const updateCalendar = () => {
    // usando o AJAX para obter os eventos agendados para o mês atual
    fetch('php/eventos-calendario.php')
        .then(response => response.json())
        .then(data => {
            eventos = data; // Armazena os eventos recebidos
            // Esse trecho faz com que seja adicionado uma classe chamada 'has-event' nos dias com eventos
            eventos.forEach(event => {
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

    const firstDay = new Date(currentYear, currentMonth, 0);
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
        const dateString = date.toISOString().split('T')[0];
        const activeClass = date.toDateString() === new Date().toDateString() ? 'active' : '';
        datesHTML += `<div class="date ${activeClass}" data-date="${dateString}">${i}</div>`;
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
});

nextBtn.addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() + 1);
    updateCalendar();
});

// Adicionar o evento de clique após o calendário ser atualizado
datesElement.addEventListener('click', function(event) {
    const target = event.target;
    
    if (target.classList.contains('has-event')) {
        const date = target.dataset.date;
        const eventosDoDia = eventos.filter(event => event.data === date);

        if (eventosDoDia.length > 0) {
            const dateModal = new bootstrap.Modal(document.getElementById('dateModal'));

            // Atualiza o conteúdo do modal com os detalhes dos eventos
            const modalTitle = document.querySelector('#dateModal .modal-title');
            const modalBody = document.querySelector('#dateModal .modal-body');
            modalTitle.innerText = `Eventos em ${formatDate(date)}`;
            modalBody.innerHTML = '';

            eventosDoDia.forEach(detalhesEvento => {
                const eventElement = document.createElement('div');
                eventElement.innerHTML = `
                    <div class='infoEvento'>
                        <p><strong>Nome:</strong> ${detalhesEvento.nome}</p>
                        <p><strong>Descrição:</strong> ${detalhesEvento.descricao}</p>
                        <p><strong>Hora de Início:</strong> ${formatTime(detalhesEvento.hr_inicio)}</p>
                        <p><strong>Hora de Término:</strong> ${formatTime(detalhesEvento.hr_termino)}</p>
                        <p><strong>Prioridade:</strong> ${detalhesEvento.prioridade_status}</p>
                    </div>
                `;
                modalBody.appendChild(eventElement);
            });

            // Exibe o modal
            dateModal.show();
        }
    }
});

updateCalendar();
