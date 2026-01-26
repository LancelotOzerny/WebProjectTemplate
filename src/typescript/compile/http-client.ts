export class HttpClient
{
    private baseUrl: string;

    constructor(baseUrl: string = '')
    {
        this.baseUrl = baseUrl.trim().replace(/\/$/, ''); // Удаляем слэш в конце
    }

    /**
     * GET-запрос
     */
    get(
        url: string,
        params?: Record<string, any>,
        onSuccess?: (response: any) => void,
        onFailure?: (status: number, error: string) => void
    ): void
    {
        this.request('GET', url, params, onSuccess, onFailure);
    }

    /**
     * POST-запрос
     */
    post(
        url: string,
        data: Record<string, any>,
        onSuccess?: (response: any) => void,
        onFailure?: (status: number, error: string) => void
    ): void
    {
        this.request('POST', url, data, onSuccess, onFailure);
    }

    /**
     * PUT-запрос
     */
    put(
        url: string,
        data: Record<string, any>,
        onSuccess?: (response: any) => void,
        onFailure?: (status: number, error: string) => void
    ): void
    {
        this.request('PUT', url, data, onSuccess, onFailure);
    }

    /**
     * DELETE-запрос
     */
    delete(
        url: string,
        params?: Record<string, any>,
        onSuccess?: (response: any) => void,
        onFailure?: (status: number, error: string) => void
    ): void
    {
        this.request('DELETE', url, params, onSuccess, onFailure);
    }

    /**
     * Общий метод для выполнения HTTP-запросов
     */
    private async request(
        method: string,
        url: string,
        data: Record<string, any> | undefined,
        onSuccess: ((response: any) => void) | undefined,
        onFailure: ((status: number, error: string) => void) | undefined
    ): Promise<void>
    {
        try
        {
            let fullUrl = this.baseUrl + url;
            let config: RequestInit = {
                method: method,
                headers: {}
            };

            if (['POST', 'PUT'].includes(method))
            {
                (config.headers as Record<string, string>)['Content-Type'] =
                    'application/x-www-form-urlencoded';

                const searchParams = new URLSearchParams();
                Object.entries(data ?? {}).forEach(([key, value]) => {
                    searchParams.append(key, String(value));
                });
                config.body = searchParams.toString();
            }

            if ((method === 'GET' || method === 'DELETE') && data)
            {
                const searchParams = new URLSearchParams();
                Object.entries(data).forEach(([key, value]) =>
                {
                    searchParams.append(key, String(value));
                });
                fullUrl += '?' + searchParams.toString();
            }

            const response = await fetch(fullUrl, config);

            if (!response.ok)
            {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }

            const result = await response.json();

            if (onSuccess)
            {
                onSuccess(result);
            }
        }
        catch (error)
        {
            const status = error instanceof Error && 'status' in error
                ? (error as any).status
                : 0;
            const message = error instanceof Error ? error.message : String(error);

            if (onFailure)
            {
                onFailure(status, message);
            }
            else
            {
                console.error('HTTP Request Error:', status, message);
            }
        }
    }
}