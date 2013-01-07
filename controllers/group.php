<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Group extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->library('ion_auth');
		$this->load->library('form_validation');
		if (!$this->ion_auth->logged_in())
		{
			//redirect them to the login page
			redirect('auth/login', 'refresh');
		}
		elseif (!$this->ion_auth->is_admin())
		{
			//redirect them to the home page because they must be an administrator to view this
			redirect('/', 'refresh');
		}
	}

	/**
	 * Index page
	 */
	function index()
	{
		$this->data['title'] = "Groups";
		//set the flash data error message if there is one
		$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

		//list the groups
		$table = array();
		$this->data['groups'] = $this->ion_auth->groups()->result();
		$this->load->view('auth/index_group', $this->data);
	}

	/**
	 * View group
	 * @param int $id Group ID
	 */
	function read($id = NULL)
	{
		$this->update($id, TRUE);
	}

	/**
	 * Create group
	 */
	function create()
	{
		$this->data['title'] = "Create Group";

		//validate form input
		$this->form_validation->set_rules('group_name', 'Group name', 'required|alpha_dash|xss_clean');
		$this->form_validation->set_rules('description', 'Description', 'xss_clean');

		if ($this->form_validation->run() == TRUE && $this->ion_auth->create_group($this->input->post('group_name'), $this->input->post('description')))
		{
			// check to see if we are creating the group
			// redirect them back to the admin page
			$this->session->set_flashdata('message', $this->ion_auth->messages());
			redirect("group", 'refresh');
		}
		else
		{
			//display the create group form
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

			$this->data['group_name'] = array('name'  => 'group_name',
											  'id'    => 'group_name',
											  'type'  => 'text',
											  'value' => $this->form_validation->set_value('group_name'),);
			$this->data['description'] = array('name'  => 'description',
											   'id'    => 'description',
											   'type'  => 'text',
											   'value' => $this->form_validation->set_value('description'),);

			$this->load->view('auth/create_group', $this->data);
		}
	}

	/**
	 * Update group
	 * @param int $id Group ID
	 * @param boolean $readonly View only?
	 */
	function update($id = NULL, $readonly = FALSE)
	{
		// bail if no group id given
		if (!$id || empty($id))
		{
			redirect('group', 'refresh');
		}

		$this->data['title'] = "Edit Group";

		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin())
		{
			redirect('auth', 'refresh');
		}

		$group = $this->ion_auth->group($id)->row();

		//validate form input
		$this->form_validation->set_rules('group_name', 'Group name', 'required|alpha_dash|xss_clean');
		$this->form_validation->set_rules('group_description', 'Group Description', 'xss_clean');

		if (isset($_POST) && !empty($_POST))
		{
			if ($this->form_validation->run() === TRUE && $this->ion_auth->update_group($id, $_POST['group_name'], $_POST['group_description']))
			{
				//check to see if we are editing the group
				//redirect them back to the groups page
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				redirect("group", 'refresh');
			}
		}

		//set the flash data error message if there is one
		$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

		//pass the group to the view
		$this->data['group'] = $group;

		$this->data['group_name'] = array('name'  => 'group_name',
										  'id'    => 'group_name',
										  'type'  => 'text',
										  'value' => $this->form_validation->set_value('group_name', $group->name),);
		$this->data['group_description'] = array('name'  => 'group_description',
												 'id'    => 'group_description',
												 'type'  => 'text',
												 'value' => $this->form_validation->set_value('group_description', $group->description),);

		if ($readonly)
		{
			foreach (array('group_name',
						   'group_description') as $disabled_field)
			{
				$this->data[$disabled_field]['disabled'] = 'disabled';
			}
		}

		$this->data['readonly'] = $readonly;

		$this->load->view('auth/edit_group', $this->data);
	}

	/**
	 * Delete group
	 * @param int $id Group ID
	 */
	function delete($id = NULL)
	{
		if ($this->ion_auth->delete_group($id))
		{
			$this->session->set_flashdata('message', $this->ion_auth->messages());
		}
		else
		{
			$this->session->set_flashdata('message', $this->ion_auth->errors());
		}

		//redirect them back to the groups page
		redirect('group', 'refresh');
	}
}
