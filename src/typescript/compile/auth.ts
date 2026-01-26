import { HttpClient } from "./http-client.js";
import { MessageManager } from "./message-manager.js";

document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.querySelector('form[action="/auth/login"]');

    if (loginForm)
    {
        loginForm.addEventListener('submit', handleRegister);
    }
});

function handleRegister(event: Event): void
{
    event.preventDefault();

    const messageManager = new MessageManager('#form-errors');
    messageManager.clearAll();

    const api = new HttpClient();

    const email = (document.getElementById('email') as HTMLInputElement).value;
    const password = (document.getElementById('password') as HTMLInputElement).value;

    api.post(
        '/api/auth/login/',
        {
            email,
            password
        },
        (response) =>
        {
            if (response.status && response.status === 'success')
            {
                window.location.href = '/';
                return;
            }

            if (response.errors && response.errors.length > 0)
            {
                for (let i = 0; i < response.errors.length; ++i)
                {
                    messageManager.addDanger(response.errors[i]);
                }

                return;
            }

            messageManager.addDanger('Ошибка при авторизации. Повторите попытку позже или обратитесь в тех поддержку!');
        },
        (status, error) =>
        {
            messageManager.addDanger(`Ошибка авторизации. Повторите попытку позже!`)
            console.error('Ошибка авторизации:', status, error);
        }
    );
}