function getCustomersHandler(callback) {
    if (this.readyState !== 4) {
        return;
    }

    if (this.status !== 200) {
        callback([]);
        return;
    }

    let result;
    try {
        result = JSON.parse(this.responseText).data;
    } catch (error) {
        console.error(error);
        callback([]);
        return;
    }

    callback(result);
}

function getCustomers() {
    return new Promise((success) => {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', './api/customers');
        xhr.onload = getCustomersHandler.bind(xhr, success);
        xhr.send();
    });
}

function getInvoicesHandler(callback) {
    if (this.readyState !== 4) {
        return;
    }

    if (this.status !== 200) {
        callback([]);
        return;
    }

    let result;
    try {
        result = JSON.parse(this.responseText).data;
    } catch (error) {
        console.error(error);
        callback([]);
        return;
    }

    callback(result);
}

function getInvoices() {
    return new Promise((success) => {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', './api/invoices');
        xhr.onload = getInvoicesHandler.bind(xhr, success);
        xhr.send();
    });
}

function deleteCustomerHandler(callback) {
    if (this.readyState !== 4) {
        return;
    }

    if (this.status === 200) {
        callback();
    }
}

function deleteCustomer(customerId) {
    return new Promise((success) => {
        const xhr = new XMLHttpRequest();
        xhr.open('DELETE', `./api/customers/${customerId}`);
        xhr.onload = deleteCustomerHandler.bind(xhr, success);
        xhr.send();
    });
}

function deleteInvoiceHandler(callback) {
    if (this.readyState !== 4) {
        return;
    }

    if (this.status === 200) {
        callback();
    }
}

function deleteInvoice(invoiceId) {
    return new Promise((success) => {
        const xhr = new XMLHttpRequest();
        xhr.open('DELETE', `./api/invoices/${invoiceId}`);
        xhr.onload = deleteInvoiceHandler.bind(xhr, success);
        xhr.send();
    });
}

function addCustomerHandler(success, failure) {
    if (this.readyState !== 4) {
        return;
    }

    if (this.status === 200) {
        success();
    } else {
        try {
            failure(`${JSON.parse(this.responseText).error} (code: 12)`);
        } catch (error) {
            failure('erreur inconnue (code: -1)');
        }
    }
}

function addCustomer(data) {
    return new Promise((success, failure) => {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', './api/customers');
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.onload = addCustomerHandler.bind(xhr, success, failure);
        xhr.send(JSON.stringify(data));
    });
}

function addInvoiceHandler(success, failure) {
    if (this.readyState !== 4) {
        return;
    }

    if (this.status === 200) {
        success();
    } else {
        try {
            failure(`${JSON.parse(this.responseText).error} (code: 12)`);
        } catch (error) {
            failure('erreur inconnue (code: -1)');
        }
    }
}

function addInvoice(data) {
    return new Promise((success, failure) => {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', './api/invoices');
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.onload = addInvoiceHandler.bind(xhr, success, failure);
        xhr.send(JSON.stringify(data));
    });
}

function previewInvoiceHandler(success, failure) {
    if (this.readyState !== 4) {
        return;
    }

    if (this.status === 200) {
        success(this.response);
    } else {
        try {
            failure(`${JSON.parse(this.responseText).error} (code: 12)`);
        } catch (error) {
            failure('erreur inconnue (code: -1)');
        }
    }
}

function previewInvoice(data) {
    return new Promise((success, failure) => {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', './api/invoices?preview=1');
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.responseType = 'blob';
        xhr.onload = previewInvoiceHandler.bind(xhr, success, failure);
        xhr.send(JSON.stringify(data));
    });
}

function createButton(emoji, clickCallback) {
    const a = document.createElement('a');
    a.setAttribute('href', '#');
    a.appendChild(document.createTextNode(emoji));
    a.addEventListener('click', clickCallback);

    return a;
}
