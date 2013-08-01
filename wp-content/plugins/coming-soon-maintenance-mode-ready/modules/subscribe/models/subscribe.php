<?php
class subscribeModelCsp extends modelCsp {
	public function create($d = array()) {
		if(isset($d['email']) && !empty($d['email'])) {
			if(is_email($d['email'])) {
				$d['email'] = trim($d['email']);
				if(!frameCsp::_()->getTable('subscribers')->exists($d['email'], 'email')) {
					if(frameCsp::_()->getTable('subscribers')->insert(array(
						'email'		=> $d['email'],
						'created'	=> dbCsp::timeToDate(),
						'ip'		=> utilsCsp::getIP(),
						'active'	=> 0,
						'token'		=> md5($d['email']. AUTH_KEY),
					))) {
						$this->sendConfirmEmail($d['email']);
						return true;
					} else
						$this->pushError( langCsp::_('Error insert email to database') );
				} else
					$this->pushError (langCsp::_('You are already subscribed'));
			} else
				$this->pushError (langCsp::_('Invalid email'));
		} else
			$this->pushError (langCsp::_('Please enter email'));
		return false;
	}
	public function sendConfirmEmail($email) {
		return frameCsp::_()->getModule('messenger')->send(
					$email, 
					get_bloginfo('name'), 
					get_bloginfo('name'), 
					'subscribe', 
					'sub_confirm', 
					array(
						'site_name' => get_bloginfo('name'),
						'link' => $this->getConfirmLink($email),
					));
	}
	public function getConfirmLink($email) {
		$token = frameCsp::_()->getTable('subscribers')->get('token', array('email' => $email), '', 'one');
		return uriCsp::_(array(
			'pl'		=> CSP_CODE,
			'mod'		=> 'subscribe',
			'action'	=> 'confirm',
			'email'		=> $email,
			'token'		=> $token,
		));
	}
	public function confirm($d = array()) {
		if(isset($d['email']) 
			&& !empty($d['email']) 
			&& isset($d['token']) 
			&& !empty($d['token'])
		) {
			$subId = frameCsp::_()->getTable('subscribers')->get('id', array('email' => $d['email'], 'token' => $d['token']), '', 'one');
			if(!empty($subId)) {
				frameCsp::_()->getTable('subscribers')->update(array('active' => 1), array('id' => $subId));
				if(!frameCsp::_()->getModule('options')->isEmpty('sub_admin_email')) {
					$this->sendAdminNotification($d['email']);
				}
				return true;
			} else
				$this->pushError (langCsp::_('No record for such email or token'));
		} else
			$this->pushError (langCsp::_('Invalid confirm data'));
		return false;
	}
	public function sendAdminNotification($email) {
		return frameCsp::_()->getModule('messenger')->send(
					frameCsp::_()->getModule('options')->get('sub_admin_email'), 
					get_bloginfo('name'), 
					get_bloginfo('name'), 
					'subscribe', 
					'sub_admin_notify', 
					array(
						'site_name' => get_bloginfo('name'),
						'email' => $email,
					));
	}
	public function getList($d = array()) {
		if(isset($d['limitFrom']) && isset($d['limitTo']))
			frameCsp::_()->getTable('subscribers')->limitFrom($d['limitFrom'])->limitTo($d['limitTo']);
		$fromDb = frameCsp::_()->getTable('subscribers')->get('*', $d);
		foreach($fromDb as $i => $val) {
			$fromDb[ $i ] = $this->prepareData($fromDb[ $i ]);
		}
		return $fromDb;
	}
	public function prepareData($data) {
		$data['status'] = (int)$data['active'] ? 'active' : 'disabled';
		return $data;
	}
	public function getCount($d = array()) {
		return frameCsp::_()->getTable('subscribers')->get('COUNT(*)', $d, '', 'one');
	}
	public function changeStatus($d = array()) {
		$d['id'] = isset($d['id']) ? (int)$d['id'] : 0;
		if($d['id']) {
			if(dbCsp::query('UPDATE @__subscribers SET active = IF(active, 0, 1) WHERE id = "'. $d['id']. '"')) {
				return true;
			} else
				$this->pushError (langCsp::_('Database error were occured'));
			return true;
		} else
			$this->pushError (langCsp::_('Invalid ID'));
		return false;
	}
	public function remove($d = array()) {
		$d['id'] = isset($d['id']) ? (int)$d['id'] : 0;
		if($d['id']) {
			if(frameCsp::_()->getTable('subscribers')->delete($d['id'])) {
				return true;
			} else
				$this->pushError (langCsp::_('Database error were occured'));
			return true;
		} else
			$this->pushError (langCsp::_('Invalid ID'));
		return false;
	}
	public function sendSiteOpenNotif() {
		// All active subscribers
		$subscribers = $this->getList(array('active' => 1));
		if(!empty($subscribers)) {
			foreach($subscribers as $s) {
				$this->sendSiteOpenNotifOne($s);
			}
		}
	}
	public function sendSiteOpenNotifOne($d = array()) {
		if(!empty($d['email'])) {
			return frameCsp::_()->getModule('messenger')->send(
					$d['email'], 
					get_bloginfo('name'), 
					get_bloginfo('name'), 
					'subscribe', 
					'sub_site_opened', 
					array(
						'site_name' => get_bloginfo('name'),
						'site_link' => get_bloginfo('url'),
					));
		}
		return false;
	}
	public function sendNewPostNotif($d = array()) {
		// All active subscribers
		$subscribers = $this->getList(array('active' => 1));
		if(!empty($subscribers)) {
			foreach($subscribers as $s) {
				$data = $s;
				$data['post_id'] = $d['post_id'];
				$this->sendNewPostNotifOne($data);
			}
		}
	}
	public function sendNewPostNotifOne($d = array()) {
		if(!empty($d['email'])) {
			return frameCsp::_()->getModule('messenger')->send(
					$d['email'], 
					get_bloginfo('name'), 
					get_bloginfo('name'), 
					'subscribe', 
					'sub_new_post', 
					array(
						'site_name' => get_bloginfo('name'),
						'post_link' => get_permalink($d['post_id']),
						'post_title' => get_the_title($d['post_id']),
					));
		}
		return false;
	}
}
