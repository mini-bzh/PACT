document.querySelectorAll('.trait').forEach(sousBloc => {
    sousBloc.style.display = 'none';
});

document.querySelectorAll('.liHeaderMobile').forEach(parent => {
    parent.addEventListener('click', () => {
        
        document.querySelectorAll('.trait').forEach(sousBloc => {
            sousBloc.style.display = 'none';
        });
        
        const sousBloc = parent.querySelector('.trait');
        sousBloc.style.display = 'block';
    });
});
