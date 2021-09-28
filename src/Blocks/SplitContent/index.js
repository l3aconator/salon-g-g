const { wp } = window;
const { registerBlockType, registerBlockStyle } = wp.blocks; // Import registerBlockType() from wp.blocks
const { MediaUpload, InnerBlocks } = wp.blockEditor;
const { Button } = wp.components;

const BLOCK_NAME = 'salon/split-content';

class SplitContentBlock {
  constructor() {
    this.register();
  }

  edit({ attributes: { imageUrl, imageID }, setAttributes }) {
    const removeImg = () => {
      setAttributes({ imageAlt: null, imageUrl: null });
    };

    const getImageButton = openFn => {
      if (imageUrl) {
        return (
          <div className="button-container">
            <button onClick={openFn} className="replace-img-button">
              <img src={imageUrl} className="image" alt="" />
            </button>
            <div className="remove-img-button">
              <Button onClick={removeImg} className="button button-large">
                Remove image
              </Button>
            </div>
          </div>
        );
      } else {
        return (
          <div className="button-container">
            <div className="empty-button-container">
              <Button onClick={openFn} className="button button-large">
                Pick an image
              </Button>
            </div>
          </div>
        );
      }
    };

    return (
      <div className="container">
        <InnerBlocks />
        <MediaUpload
          onSelect={media => {
            setAttributes({ imageAlt: media.alt, imageUrl: media.url });
          }}
          type="image"
          value={imageID}
          render={({ open }) => getImageButton(open)}
        />
      </div>
    );
  }

  save({ attributes: { imageUrl, imageAlt } }) {
    return (
      <div>
        <div className={`block__split-content ${imageUrl ? 'single__grid' : ''}`}>
          {imageUrl && (
            <figure className="single__col single__figure">
              <img className="split-content__image" src={imageUrl} alt={imageAlt} />
            </figure>
          )}
          <div
            className={`split-content__body content ${
              imageUrl ? 'single__col' : 'single-content--no-img'
            }`}>
            <InnerBlocks.Content />
          </div>
        </div>
      </div>
    );
  }

  register() {
    registerBlockType(BLOCK_NAME, {
      title: 'Text/Image Split', // Block title.
      icon: 'align-pull-right',
      category: 'common',
      keywords: ['text', 'image', 'grid'],
      attributes: {
        body: {
          type: 'array',
          source: 'children',
          selector: '.split-content__body',
        },
        imageAlt: {
          attribute: 'alt',
          selector: '.split-content__image',
        },
        imageUrl: {
          attribute: 'src',
          selector: '.split-content__image',
        },
      },
      edit: this.edit,
      save: this.save,
    });

    registerBlockStyle(BLOCK_NAME, {
      name: 'full-width',
      label: 'Full-width container',
    });
  }
}

export default SplitContentBlock;
