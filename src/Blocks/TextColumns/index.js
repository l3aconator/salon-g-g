const { wp } = window;
const { registerBlockType } = wp.blocks; // Import registerBlockType() from wp.blocks
const { RichText } = wp.blockEditor;
const { Button } = wp.components;

class SplitContentBlock {
  constructor() {
    this.register();
  }

  edit({ attributes: { col1, col2, col3, colCount }, setAttributes }) {
    const addCol = () => {
      setAttributes({ colCount: 3 });
    };

    const removeCol = () => {
      setAttributes({ colCount: 2 });
    };

    return (
      <div>
        <div className="container">
          <div className="text-columns">
            <RichText
              onChange={content => setAttributes({ col1: content })}
              value={col1}
              multiline="p"
              placeholder="Enter text for the first column"
            />
            <RichText
              onChange={content => setAttributes({ col2: content })}
              value={col2}
              multiline="p"
              placeholder="Enter text for the second column"
            />

            {colCount > 2 && (
              <RichText
                onChange={content => setAttributes({ col3: content })}
                value={col3}
                multiline="p"
                placeholder="Enter text for the third column"
              />
            )}
          </div>

          <div className="button-container">
            {colCount > 2 ? (
              <Button className="button button-large" onClick={removeCol}>
                Remove column
              </Button>
            ) : (
              <Button className="button button-large" onClick={addCol}>
                Add column
              </Button>
            )}
          </div>
        </div>
      </div>
    );
  }

  save({ attributes: { col1, col2, col3, colCount } }) {
    return (
      <div>
        <div className="site-wrapper">
          <div className="text-columns__wrapper">
            {col1 && col1.length > 0 && <div className="text-columns__col col1">{col1}</div>}
            {col2 && col2.length > 0 && <div className="text-columns__col col2">{col2}</div>}
            {col3 && col3.length > 0 && colCount > 2 && (
              <div className="text-columns__col col3">{col3}</div>
            )}
          </div>
        </div>
      </div>
    );
  }

  register() {
    registerBlockType('salon/text-columns', {
      title: 'Text Columns',
      icon: 'columns',
      category: 'widgets',
      keywords: ['text', 'columns', 'layout'],
      attributes: {
        col1: {
          type: 'array',
          source: 'children',
          selector: '.col1',
        },
        col2: {
          type: 'array',
          source: 'children',
          selector: '.col2',
        },
        col3: {
          type: 'array',
          source: 'children',
          selector: '.col3',
        },
        colCount: {
          type: 'number',
          default: 2,
        },
      },
      edit: this.edit,
      save: this.save,
    });
  }
}

export default SplitContentBlock;
