const allowedTags = new Set(['P', 'DIV', 'STRONG', 'B', 'EM', 'I', 'U', 'UL', 'OL', 'LI', 'H2', 'H3', 'BLOCKQUOTE', 'BR']);
export function sanitizeTripHtml(html = '') {
    const parsed = new DOMParser().parseFromString(String(html), 'text/html');
    const clean = document.createElement('div');
    const copy = (source, target) => {
        for (const node of source.childNodes) {
            if (node.nodeType === Node.TEXT_NODE) target.append(document.createTextNode(node.textContent));
            else if (node.nodeType === Node.ELEMENT_NODE && !['SCRIPT', 'STYLE', 'IFRAME', 'SVG', 'MATH', 'OBJECT', 'TEMPLATE'].includes(node.tagName)) {
                const element = allowedTags.has(node.tagName) ? document.createElement(node.tagName.toLowerCase()) : document.createDocumentFragment();
                copy(node, element);
                target.append(element);
            }
        }
    };
    copy(parsed.body, clean);
    return clean.innerHTML;
}
export function plainTripHtml(html = '') {
    const parsed = new DOMParser().parseFromString(sanitizeTripHtml(html).replace(/<\/(p|div|li|h2|h3|blockquote)>|<br>/g, '\n'), 'text/html');
    return parsed.body.textContent.trim();
}
export function textToTripHtml(text = '') {
    const element = document.createElement('div');
    element.textContent = text;
    return element.innerHTML.replace(/\n/g, '<br>');
}
