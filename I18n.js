export default class I18n
{
    /**
     * Initialize a new translation instance.
     */
    constructor(translations = {})
    {
        this.translations = translations;
    }

    /**
     * Get and replace the string of the given key.
     */
    trans(key, replace = {})
    {
        return this._replace(this._extract(key), replace);
    }

    /**
     * Get and pluralize the strings of the given key.
     */
    trans_choice(key, count = 1, replace = {})
    {
        let translation = this._extract(key, '|').split('|');

        translation = count > 1 ? translation[1] : translation[0];

        return this._replace(translation, replace);
    }

    /**
     * Replace the placeholders.
     */
    _replace(translation, replace)
    {
        for (let placeholder in replace) {
            translation = translation.replace(`:${placeholder}`, replace[placeholder]);
        }

        return translation;
    }

    /**
     * The extract helper.
     */
    _extract(key, value = null)
    {
        return key.toString().split('.').reduce((t, i) => t[i] || (value || key), this.translations);
    }
}
