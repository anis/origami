module.exports = {
    '/customers': {
        get: require('./customers/list'),
        post: require('./customers/add'),
    },
    '/customers/:id': {
        get: require('./customers/get'),
        delete: require('./customers/delete'),
    },
    '/invoices': {
        get: require('./invoices/list'),
        post: require('./invoices/add'),
    },
    '/invoices/:id': {
        get: require('./invoices/get'),
        delete: require('./invoices/delete'),
    },
};
