import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, CheckboxControl } from '@wordpress/components';
import { useState, useEffect } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import metadata from './block.json';
import './style.scss'; // Import the SCSS file

const COLUMN_MAP = {
	ID: 'id',
	'First Name': 'fname',
	'Last Name': 'lname',
	Email: 'email',
	Date: 'date',
};

const getColumnNamesByHeader = ( headerName ) =>
	COLUMN_MAP[headerName] || null;

const formatDate = ( timestamp ) => {
	const date = new Date( timestamp * 1000 ); // Convert to milliseconds
	return date.toLocaleDateString( 'en-US' );
};

const fetchData = async () => {
	const URL  = window.wp.ajax.settings.url;
	const body = new FormData();
	body.append( 'action', 'manzur_test_plugin_api_data' );
	body.append( 'security', manzurSettings.nonce );

	const response = await fetch( URL, {
		method: 'POST',
		body,
	} );

	if ( !response.ok ) {
		throw new Error( 'Network response was not ok' );
	}

	const result = await response.json();
	return result.data;
};

const ColumnVisibilityControl = ( {
	                                  headers,
	                                  visibleColumns,
	                                  toggleColumnVisibility,
                                  } ) => (
	<InspectorControls>
		<PanelBody title={__( 'Column Visibility', 'manzur-test-plugin' )}>
			{headers.map( ( header, index ) => (
				<CheckboxControl
					key={index}
					label={header}
					checked={
						visibleColumns[getColumnNamesByHeader( header )]
					}
					onChange={() =>
						toggleColumnVisibility(
							getColumnNamesByHeader( header )
						)
					}
				/>
			) )}
		</PanelBody>
	</InspectorControls>
);

const DataTable = ( { headers, rows, visibleColumns, blockProps } ) => (
	<table {...blockProps}>
		<thead>
		<tr>
			{headers.map(
				( header, index ) =>
					visibleColumns[getColumnNamesByHeader( header )] && (
						                                                 <th key={index}>{header}</th>
					                                                 )
			)}
		</tr>
		</thead>
		<tbody>
		{Object.keys( rows ).map( ( key ) => {
			const { id, fname, lname, email, date } = rows[key];
			return (
				<tr key={id}>
					{visibleColumns.id && <td>{id}</td>}
					{visibleColumns.fname && <td>{fname}</td>}
					{visibleColumns.lname && <td>{lname}</td>}
					{visibleColumns.email && <td>{email}</td>}
					{visibleColumns.date && (
						<td>{formatDate( date )}</td>
					)}
				</tr>
			);
		} )}
		</tbody>
	</table>
);

registerBlockType( metadata.name, {
	edit: ( { attributes, setAttributes } ) => {
		const blockProps            = useBlockProps();
		const [data, setData]       = useState( null );
		const [loading, setLoading] = useState( true );
		const [error, setError]     = useState( null );
		useEffect( () => {
			const loadData = async () => {
				try {
					const result = await fetchData();
					setData( result );
					setAttributes( { tableData: result } ); // Save the fetched data
				} catch ( error ) {
					setError( error );
				} finally {
					setLoading( false );
				}
			};

			loadData();
		}, [setAttributes] );

		const toggleColumnVisibility = ( column ) => {
			setAttributes( {
				               visibleColumns: {
					               ...attributes.visibleColumns,
					               [column]: !attributes.visibleColumns[column],
				               },
			               } );
		};

		if ( loading ) {
			return <div>{__( 'Loading...', 'manzur-test-plugin' )}</div>;
		}

		if ( error ) {
			return (
				<div>
					{sprintf(
						__( 'Error: %s', 'manzur-test-plugin' ),
						error.message
					)}
				</div>
			);
		}

		if ( !data ) {
			return (
				<div>{__( 'No data available', 'manzur-test-plugin' )}</div>
			);
		}

		const { headers, rows } = data.data;

		return (
			<>
				<ColumnVisibilityControl
					headers={headers}
					visibleColumns={attributes.visibleColumns}
					toggleColumnVisibility={toggleColumnVisibility}
				/>
				<DataTable
					headers={headers}
					rows={rows}
					visibleColumns={attributes.visibleColumns}
					blockProps={blockProps}
				/>
			</>
		);
	},
	save: ( { attributes } ) => {
		const { visibleColumns, tableData } = attributes;
		const blockProps                    = useBlockProps.save();

		if ( !tableData ) {
			return null;
		}

		const { headers, rows } = tableData.data;

		return (
			<DataTable
				headers={headers}
				rows={rows}
				visibleColumns={visibleColumns}
				blockProps={blockProps}
			/>
		);
	},
} );
