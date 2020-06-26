import DescriptionComponent from './description-component.js';

export const DescriptionControl = wp.customize.astraControl.extend( {
	renderContent: function renderContent() {
		let control = this;
	ReactDOM.render( <DescriptionComponent control={ control } />, control.container[0] );
	}
} );