document.querySelectorAll('.liHeaderMobile').forEach(parent => {
    parent.addEventListener('click', () => {
        
        const sousBloc = parent.querySelector('.trait');
        
        if (sousBloc.style.display === 'block') {
            sousBloc.style.display = 'none';
        } else {
            sousBloc.style.display = 'block';
        }
    });
});
