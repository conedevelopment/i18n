export default class I18n
{
    /**
     * Get and replace the string of the given key.
     */
    static trans(key, replace = {}) 
    {
        let translation = this.extract(key, window.translations);

        return this.replace(translation, replace);
    }

    /**
     * Get and pluralize the strings of the given key.
     */
    static trans_choice(key, count = 1, replace = {})
    {
        let translation = this.extract(key, window.translations, '|').split('|');

        translation = count > 1 ? translation[1] : translation[0];

        return this.replace(translation, replace);
    }

    /**
     * Replace the placeholders.
     */
    static replace(translation, replace)
    {
        for (var placeholder in replace) {
            translation = translation.replace(`:${placeholder}`, replace[placeholder]);
        }

        return translation;
    }

    /**
     * Extract the value from an object by the given key.
     */
    static extract(key, object, def = null)
    {
        return key.split('.').reduce((t, i) => t[i] || def, object);
    }
}
