export const DELETE_PARAMETERS = {
    method: 'DELETE',
    credentials: 'include',
    headers: {
        'Content-Type': 'application/json',
    }
};

export const GET_PARAMETERS = {
    credentials: 'include',
    headers: {
        'Content-Type': 'application/json',
    }
};

export const POST_PARAMETERS = {
    method: 'POST',
    cache: 'no-cache',
    credentials: 'include',
    headers: {
        'Content-Type': 'application/json'
    }
};

export const PUT_PARAMETERS = {
    method: 'PUT',
    cache: 'no-cache',
    credentials: 'include',
    headers: {
        'Content-Type': 'application/json'
    }
};


export function post(request, fields) {
    return Object.assign({},
        POST_PARAMETERS,
        {body: JSON.stringify(request)});
}

export function put(request) {
    return Object.assign({},
        PUT_PARAMETERS,
        {body: JSON.stringify(request)});
}

export function jsonOrThrow(response) {
    if (response && response.ok) {
        return response.json();
    } else {
        throw response ? response.status : 500;
    }
}

export function okOrThrow(response) {
    if (response && response.ok) {
        return response;
    } else {
        throw response ? response.status : 500;
    }
}

export function catchStatusError(error) {
    alert(error);
}