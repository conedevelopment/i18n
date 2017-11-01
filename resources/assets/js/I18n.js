export default class I18n
{
    /**
     * Initialize a new translation instance.
     * 
     * @param  {string}  key
     * @return {void}
     */
    constructor(key = 'translations')
    {
        this.key = key;
    }

    /**
     * Get and replace the string of the given key.
     * 
     * @param  {string}  key
     * @param  {object}  replace
     * @return {string}
     */
    trans(key, replace = {})
    {
        return this._replace(this._extract(key), replace);
    }

    /**
     * Get and pluralize the strings of the given key.
     * 
     * @param  {string}  key
     * @param  {number}  count
     * @param  {object}  replace
     * @return {string}
     */
    trans_choice(key, count = 1, replace = {})
    {
        let translation = this._extract(key, '|').split('|');

        translation = count > 1 ? translation[1] : translation[0];

        return this._replace(translation, replace);
    }

    /**
     * Replace the placeholders.
     * 
     * @param  {string}  translation
     * @param  {object}  replace
     * @return {string}
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
     * 
     * @param  {string}  key
     * @param  {mixed}  value
     * @return {mixed}
     */
    _extract(key, value = null)
    {
        return key.toString().split('.').reduce((t, i) => t[i] || (value || key), window[this.key]);
    }
}
