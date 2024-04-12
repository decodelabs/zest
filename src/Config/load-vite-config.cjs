const loadConfigFromFile = require('vite').loadConfigFromFile;

module.exports = async function loadConfig(path) {
    return await loadConfigFromFile({}, path);
}
