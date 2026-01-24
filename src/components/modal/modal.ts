export class ModalWindow
{
    private modal : HTMLElement | null = null;

    constructor(private modalName : string)
    {
        this.modal = document.querySelector('*[data-modal-name="' + this.modalName + '"]')

        this.setStyles();
        this.setOpenButtons();
        this.setCloseButtons();
    }

    setStyles(): void
    {
        if (!this.modal)
        {
            return;
        }

        this.modal.style.left = -(this.modal.clientWidth + 10) + 'px';
    }

    setCloseButtons(): void
    {
        let buttons: NodeListOf<Element> = document.querySelectorAll('*[data-modal-close="' + this.modalName + '"]');
        buttons.forEach(btn => btn.addEventListener('click', this.close.bind(this)));
    }

    setOpenButtons(): void
    {
        let buttons: NodeListOf<Element> = document.querySelectorAll('*[data-modal-open="' + this.modalName + '"]');
        buttons.forEach(btn => btn.addEventListener('click', this.open.bind(this)));
    }

    open(): void
    {
        if (this.modal === null)
        {
            return;
        }

        if (!this.modal.parentElement?.classList.contains('active'))
        {
            this.modal.parentElement?.classList.add('active');
        }

        this.modal.style.left = 'calc(50% - ' + this.modal.clientWidth / 2 + 'px )'
        this.modal.style.top = 'calc(50% - ' + this.modal.clientHeight / 2 + 'px )'
    }

    close(): void
    {
        if (this.modal === null)
        {
            return;
        }

        if (this.modal.parentElement?.classList.contains('active'))
        {
            this.modal.parentElement?.classList.remove('active');
        }

        this.modal.style.left = -(this.modal.clientWidth + 10) + 'px';
    }
}