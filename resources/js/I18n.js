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
        let translations = this._extract(key, '|').split('|'), translation;

        translations.some(t => translation = this._match(t, count));

        if (!translation) {
            // TODO: support other plural forms
            let pluralIndex = (count == 1) ? 0 : 1;
            translation = (pluralIndex in translations) ? translations[pluralIndex] : translations[0]
        }

        translation = translation.replace(/\[.*?\]|\{.*?\}/, '');

        replace['count'] = count;
        
        return this._replace(translation, replace);
    }

    /**
     * Match the translation limit with the count.
     *
     * @param  {string}  translation
     * @param  {number}  count
     * @return {string|null}
     */
    _match(translation, count)
    {
        let match = translation.match(/^[\{\[]([^\[\]\{\}]*)[\}\]](.*)/);

        if (! match) return;

        if (match[1].includes(',')) {
            let [from, to] = match[1].split(',', 2);

            if (to === '*' && count >= from) {
                return match[2];
            } else if (from === '*' && count <= to) {
                return match[2];
            } else if (count >= from && count <= to) {
                return match[2];
            }
        }

        return match[1] == count ? match[2] : null;
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
        if (typeof translation === 'object') {
            return translation;
        }

        for (let placeholder in replace) {
            translation = translation.toString()
                .replace(`:${placeholder}`, replace[placeholder])
                .replace(`:${placeholder.toUpperCase()}`, replace[placeholder].toString().toUpperCase())
                .replace(
                    `:${placeholder.charAt(0).toUpperCase()}${placeholder.slice(1)}`,
                    replace[placeholder].toString().charAt(0).toUpperCase() + replace[placeholder].toString().slice(1)
                );
        }

        return translation.toString().trim()
    }

    /**
     * Extract values from objects by dot notation.
     *
     * @param  {string}  key
     * @param  {mixed}  value
     * @return {mixed}
     */
    _extract(key, value = null)
    {
        let path = key.toString().split('::'),
            keys = path.pop().toString().split('.');

        if (path.length > 0) {
            path[0] += '::';
        }

        return path.concat(keys).reduce((t, i) => t[i] || (value || key), window[this.key]);
    }
}
