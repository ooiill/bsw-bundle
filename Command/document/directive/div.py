import sys
from docutils import nodes
from docutils.parsers.rst import Directive
from docutils.parsers.rst import directives

def setup(app):
    app.add_node(DivNode, html=(DivNode.visit_div, DivNode.depart_div))
    app.add_directive('div', DivDirective)
    return {'version': '0.1'}

class DivNode(nodes.General, nodes.Element):

    def __init__(self, text):
        super(DivNode, self).__init__()

    @staticmethod
    def visit_div(self, node):
        self.body.append(self.starttag(node, 'div'))

    @staticmethod
    def depart_div(self, node=None):
        self.body.append('</div>')

class DivDirective(Directive):

    optional_arguments = 1
    final_argument_whitespace = True
    option_spec = {'name': directives.unchanged}
    has_content = True

    def run(self):
        self.assert_has_content()
        try:
            if self.arguments:
                classes = directives.class_option(self.arguments[0])
            else:
                classes = []
        except ValueError:
            raise self.error(
                'Invalid class attribute value for "%s" directive: "%s".'
                % (self.name, self.arguments[0]))
        node = DivNode(self.content)
        node['classes'].extend(classes)
        self.add_name(node)
        self.state.nested_parse(self.content, self.content_offset, node)
        return [node]