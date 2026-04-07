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
        let segments = this._extract(key).toString().split('|');
        let translation = this._extractChoice(segments, count);

        return this._replace(translation, { count, ...replace });
    }

    /**
     * Extract a translation string using inline conditions.
     *
     * @param  {string[]}  segments
     * @param  {number}  count
     * @return {string}
     */
    _extractChoice(segments, count)
    {
        let translation;

        segments.some(segment => {
            translation = this._extractFromString(segment, count);

            return translation !== null;
        });

        if (translation !== null && translation !== undefined) {
            return translation.trim();
        }

        segments = this._stripConditions(segments);

        if (segments.length === 1 || count == 1 || segments[1] === undefined) {
            return segments[0].trim();
        }

        return segments[1].trim();
    }

    /**
     * Get the translation string if the condition matches.
     *
     * @param  {string}  translation
     * @param  {number}  count
     * @return {string|null}
     */
    _extractFromString(translation, count)
    {
        let match = translation.match(/^[\{\[]([^\[\]\{\}]*)[\}\]]([\s\S]*)/);

        if (! match || match.length !== 3) {
            return null;
        }

        let condition = match[1];
        let value = match[2];

        if (condition.includes(',')) {
            let [from, to] = condition.split(',', 2);

            if (to === '*' && count >= from) {
                return value;
            } else if (from === '*' && count <= to) {
                return value;
            } else if (count >= from && count <= to) {
                return value;
            }
        }

        return condition == count ? value : null;
    }

    /**
     * Strip the inline conditions from each segment, just leaving the text.
     *
     * @param  {string[]}  segments
     * @return {string[]}
     */
    _stripConditions(segments)
    {
        return segments.map(segment => segment.replace(/^[\{\[]([^\[\]\{\}]*)[\}\]]/, ''));
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

        return translation.toString().trim();
    }

    /**
     * Extract values from objects by dot notation.
     *
     * @param  {string}  key
     * @return {mixed}
     */
    _extract(key)
    {
        let path = key.toString().split('::');
        let keys = path.pop().toString().split('.');

        if (path.length > 0) {
            path[0] += '::';
        }

        return path.concat(keys).reduce((translations, index) => {
            return translations && translations[index] !== undefined ? translations[index] : key;
        }, window[this.key]);
    }
}
