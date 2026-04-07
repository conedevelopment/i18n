import test from 'node:test';
import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';

const source = await readFile(new URL('../resources/js/I18n.js', import.meta.url), 'utf8');
const { default: I18n } = await import(`data:text/javascript,${encodeURIComponent(source)}`);

function makeTranslator({
    translations = {},
    key = 'translations',
} = {}) {
    globalThis.window = globalThis;
    globalThis[key] = translations;

    return new I18n(key);
}

test.afterEach(() => {
    delete globalThis.translations;
    delete globalThis.custom;
    delete globalThis.window;
});

test('trans_choice matches fallback for a single segment and replaces :count', () => {
    let i18n = makeTranslator();

    assert.equal(i18n.trans_choice(':count messages', 2), '2 messages');
});

test('trans_choice keeps explicit selector behavior', () => {
    let i18n = makeTranslator();

    let cases = [
        ['first', '{0}  first|{1}second', 0],
        ['first', '{1}first|{2}second', 1],
        ['second', '{1}first|{2}second', 2],
        ['', '{0}|{1}second', 0],
        ['', '{0}first|{1}', 1],
        ['first', '{1.3}first|{2.3}second', 1.3],
        ['second', '{1.3}first|{2.3}second', 2.3],
        ['second', '[4,*]first|[1,3]second', 1],
        ['first', '[4,*]first|[1,3]second', 100],
        ['second', '[5,*]first|[*,4]second', 0],
        ['first', '{0}first|[1,3]second|[4,*]third', 0],
        ['second', '{0}first|[1,3]second|[4,*]third', 1],
        ['third', '{0}first|[1,3]second|[4,*]third', 9],
        ['first', '{0}  first | { 1 } second', 0],
        ['first', '[4,*]first | [1,3]second', 100],
    ];

    for (let [expected, line, count] of cases) {
        assert.equal(i18n.trans_choice(line, count), expected);
    }
});

test('trans_choice keeps multiline explicit selections intact', () => {
    let i18n = makeTranslator();

    assert.equal(
        i18n.trans_choice('{1}first\n            line|{2}second', 1),
        'first\n            line'
    );
});

test('trans_choice falls back to first form for 1 and second form otherwise', () => {
    let i18n = makeTranslator();

    assert.equal(i18n.trans_choice('first|second', 1), 'first');
    assert.equal(i18n.trans_choice('first|second', 0), 'second');
    assert.equal(i18n.trans_choice('first|second', 9), 'second');
});

test('trans_choice falls back to the first form when the second slot is missing', () => {
    let i18n = makeTranslator();

    assert.equal(i18n.trans_choice('first', 1), 'first');
    assert.equal(i18n.trans_choice('first', 10), 'first');
});

test('custom translation keys still work with simplified fallback behavior', () => {
    let i18n = makeTranslator({
        key: 'custom',
        translations: { auth: { attempts: 'first|second|third' } },
    });

    assert.equal(i18n.trans_choice('auth.attempts', 1), 'first');
    assert.equal(i18n.trans_choice('auth.attempts', 5), 'second');
});
