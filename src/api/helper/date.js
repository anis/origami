function toString(date, format = 'fr') {
    const day = date.getDate();
    const month = date.getMonth() + 1;
    const year = date.getFullYear();

    if (format === 'fr') {
        return `${(day < 10 ? '0' : '') + day}/${(month < 10 ? '0' : '') + month}/${year}`;
    }

    return `${(month < 10 ? '0' : '') + month}/${(day < 10 ? '0' : '') + day}/${year}`;
}

module.exports = {
    toString,

    fromTimestamp(ts, format = 'fr') {
        return toString(new Date(ts * 1000), format);
    },
};
